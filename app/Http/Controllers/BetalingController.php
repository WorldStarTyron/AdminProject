<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Betaling;
use App\Models\Gebruiker;
use App\Models\Activiteit;
use App\Models\Lid;
use App\Models\Notificatie;
use App\Http\Controllers\BonController;
use Carbon\Carbon;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

// Betalingen beheer
class BetalingController extends Controller
{
    // Betalingen overzichtspagina
    public function index(Request $request)
    {
        Gate::authorize('betalingen-bekijken');

        // Huidige maand/jaar en zoekterm uit de URL
        $maand  = (int) $request->input('maand', now()->month);
        $jaar   = (int) $request->input('jaar', now()->year);
        $search = $request->input('search');

        // Query met optionele zoekfilter (op naam, email of telefoon)
        // Alleen actieve leden, zodat er geen schuld ontstaat voor gedeactiveerde accounts
        $ledenQuery = Lid::with('gebruiker')
            ->join('gebruikers', 'leden.gebruiker_id', '=', 'gebruikers.gebruiker_id')
            ->where('gebruikers.status', 'Actief')
            ->select('leden.*');

        if (!empty($search)) {
            $ledenQuery->where(function ($q) use ($search) {
                $q->where('gebruikers.naam', 'LIKE', "%{$search}%")
                  ->orWhere('gebruikers.email', 'LIKE', "%{$search}%")
                  ->orWhere('leden.telefoonnummer', 'LIKE', "%{$search}%");
            });
        }

        $leden = $ledenQuery->get();

        // Voor toekomstige maanden alleen kijken, niets aanmaken (voorkomt spookschuld via de URL)
        $periodeIsToekomst = $jaar > now()->year || ($jaar == now()->year && $maand > now()->month);

        // Maak per lid een statusrij voor de tabel
        $ledenStatus = [];
        foreach ($leden as $lid) {
            $zoekVelden = [
                'lid_id' => $lid->lid_id,
                'maand'  => $maand,
                'jaar'   => $jaar,
            ];
            $standaardWaarden = [
                'status'          => 'Openstaand',
                'bedrag'          => $lid->MaandelijkseBijdrage(),
                'methode'         => null,
                'ingediend_op'    => Carbon::createFromDate($jaar, $maand, 1)->format('Y-m-d'),
                'betaling_bewijs' => null,
            ];

            // firstOrCreate: maakt automatisch een Openstaande betaling als er nog geen is
            if ($periodeIsToekomst) {
                $betaling = Betaling::firstOrNew($zoekVelden, $standaardWaarden);
            } else {
                $betaling = Betaling::firstOrCreate($zoekVelden, $standaardWaarden);
            }

            // Bedrag aanvullen als het 0 was (oude records)
            if ($betaling->exists && $betaling->bedrag == 0 && $lid->MaandelijkseBijdrage() > 0) {
                $betaling->bedrag = $lid->MaandelijkseBijdrage();
                $betaling->save();
            }

            $ledenStatus[] = (object) [
                'naam'                => $lid->gebruiker->naam ?? 'Onbekend',
                'status'              => $betaling->status,
                'lid_type'            => $lid->lid_type,
                'maandelijks_bijdrage'=> $lid->MaandelijkseBijdrage(),
                'bedrag'              => $lid->MaandelijkseBijdrage(),
                'betaling_id'         => $betaling->betaling_id,
            ];
        }

        // Recente betaalde transacties met optionele zoekfilter.
        // Net als op de Bewijs Ontvangen-pagina ($recentReviews) worden maanden uit
        // één betaalbewijs (zelfde betaling_bewijs) samengevoegd tot één transactie
        // met het totaalbedrag en de periode. Handmatige betalingen zonder bewijs
        // blijven losse rijen dankzij COALESCE(betaling_bewijs, betaling_id).
        $recenteQuery = Betaling::query()
            ->select(
                'gebruikers.naam',
                'betalingen.lid_id',
                'betalingen.jaar',
                DB::raw('SUM(betalingen.bedrag) as bedrag'),
                DB::raw('COUNT(*) as aantal'),
                DB::raw('MIN(betalingen.maand) as eerste_maand'),
                DB::raw('MAX(betalingen.maand) as laatste_maand'),
                DB::raw('MAX(betalingen.methode) as methode'),
                DB::raw('MAX(betalingen.status) as status'),
                DB::raw('MAX(betalingen.ingediend_op) as ingediend_op'),
                DB::raw('MAX(betalingen.betaling_id) as betaling_id'),
                DB::raw('MAX(bonnen.bon_nummer) as bon_nummer')
            )
            ->join('leden',      'leden.lid_id',           '=', 'betalingen.lid_id')
            ->join('gebruikers', 'gebruikers.gebruiker_id', '=', 'leden.gebruiker_id')
            ->leftJoin('bonnen', 'bonnen.betaling_id', '=', 'betalingen.betaling_id')
            ->where('betalingen.status', 'betaald')
            ->groupBy(
                DB::raw('COALESCE(betalingen.betaling_bewijs, betalingen.betaling_id)'),
                'betalingen.lid_id',
                'gebruikers.naam',
                'betalingen.jaar'
            );

        if (!empty($search)) {
            // Bij zoekopdracht: zoek door alle betaalde transacties (geen 30 dagen limiet)
            $recenteQuery->where(function ($q) use ($search) {
                $q->where('gebruikers.naam', 'LIKE', "%{$search}%")
                  ->orWhere('betalingen.methode', 'LIKE', "%{$search}%")
                  ->orWhere('bonnen.bon_nummer', 'LIKE', "%{$search}%");
            });
            $limit = 20;
        } else {
            // Standaard: laatste 30 dagen, max 5
            $recenteQuery->where('betalingen.ingediend_op', '>=', now()->subDays(30));
            $limit = 5;
        }

        $recenteBetalingen = $recenteQuery
            ->orderByDesc('ingediend_op')
            ->take($limit)
            ->get();

        // Totaal voor de stats kaart
        $maandTotaal = Betaling::whereIn('status', ['betaald', 'goed_gekeurd'])
            ->where('maand', $maand)
            ->where('jaar', $jaar)
            ->sum('bedrag');

        return view('BetalingPagina', compact(
            'ledenStatus',
            'recenteBetalingen',
            'maandTotaal',
            'maand',
            'jaar',
            'search'
        ));
    }







    // Bedrag per dag voor de grafiek
    public function chartData(Request $request)
    {
        $maand = (int) $request->input('maand', now()->month);
        $jaar  = (int) $request->input('jaar',  now()->year);

        $startDatum = Carbon::createFromDate($jaar, $maand, 1)->startOfMonth();
        $eindDatum  = $startDatum->copy()->endOfMonth();
        $aantalDagen = $eindDatum->day;

        // Filter op de maand WAARVOOR betaald is (maand/jaar), net als de tabel en de
        // "Inkomsten deze maand"-kaart. Niet op ingediend_op, want één betaling kan
        // meerdere maanden dekken en op een latere datum zijn geregistreerd.
        $betalingen = Betaling::select(
            DB::raw('DAY(ingediend_op) as dag'),
            DB::raw('SUM(bedrag) as totaal_bedrag')
        )
        ->whereIn('status', ['betaald', 'goed_gekeurd'])
        ->where('maand', $maand)
        ->where('jaar', $jaar)
        ->groupBy('dag')
        ->get();

        // Verdeel het bedrag over de dagen. Valt de registratiedag buiten deze maand
        // (bv. betaling op 30 juni voor februari), bundel hem dan op de laatste dag,
        // zodat het totaal van het diagram altijd gelijk blijft aan de database.
        $perDag = [];
        foreach ($betalingen as $row) {
            $dag = min((int) $row->dag, $aantalDagen);
            $perDag[$dag] = ($perDag[$dag] ?? 0) + (float) $row->totaal_bedrag;
        }

        $labels = [];
        $data   = [];

        // Vul alle dagen, ook lege
        for ($dag = 1; $dag <= $aantalDagen; $dag++) {
            $labels[] = (string) $dag;
            $data[]   = (float) ($perDag[$dag] ?? 0);
        }

        return response()->json([
            'labels' => $labels,
            'series' => [
                ['name' => 'Totaal Bedrag', 'data' => $data]
            ],
        ]);
    }

    // Nieuwe betaling opslaan
    public function store(Request $request)
    {
        Gate::authorize('betalingen-beheren');

        $request->validate([
            'naam'            => 'required|string',
            'datum'           => 'required|date',
            'methode'         => 'required|in:fysiek,overmaking',
            'status'          => 'required|in:Openstaand,in_afwachting,betaald,niet_betaald',
            'bedrag'          => 'required|numeric|min:150',// Minimaal 150 (vaste contributie)
            'betaling_bewijs' => 'nullable|pdf|max:5120',
        ], [
            'bedrag.min' => 'Het bedrag moet minimaal SRD150 zijn',
        ]);

        // Lid zoeken op naam
        $gebruiker = Gebruiker::where('naam', $request->naam)->first();

        if (!$gebruiker || !$gebruiker->lid) {
            return response()->json([
                'errors' => [
                    'naam' => ['Dit lid bestaat niet. Controleer de naam en probeer opnieuw.']
                ]
            ], 422);
        }

        $lid   = $gebruiker->lid;
        $datum = Carbon::parse($request->datum);
        $maand = $datum->month;
        $jaar  = $datum->year;

        $bedrag = $request->bedrag;

        // Bewijs opslaan als geupload, met leesbare bestandsnaam (naam-lidid-datum)
        $bewijsPath = null;
        if ($request->hasFile('betaling_bewijs')) {
            $file = $request->file('betaling_bewijs');

            $bestandsnaam = Str::slug($gebruiker->naam)     //zet naam om naar kleine letters en koppel aan elkaar
            . '-lid-' . $lid->lid_id                        //zet lid-id aan elkaar
            . '-' . now()->format('Ymd-His')                //"-20260707-1530-45"
            . '-betalingsbewijs'                            //zet betalingsbewijs aan elkaar
            . '.' . $file->getClientOriginalExtension();     //pdf

            $bewijsPath = $file->storeAs('bewijzen', $bestandsnaam, config('filesystems.bewijs_disk'));
        }

        // updateOrCreate voorkomt dubbele betalingen
        $betaling = Betaling::updateOrCreate(
            [
                'lid_id' => $lid->lid_id,
                'maand'  => $maand,
                'jaar'   => $jaar,
            ],
            [
                'bedrag'          => $bedrag,
                'methode'         => $request->methode,
                'status'          => $request->status,
                'betaling_bewijs' => $bewijsPath,
                'ingediend_op'    => $datum->format('Y-m-d'),
            ]
        );

        // Volgende deadline berekenen als status betaald is
        if (in_array($request->status, ['betaald', 'goed_gekeurd'])) {
            $betaling->berekenVolgendeDeadline($datum->format('Y-m-d'));

            // Geschorst lid weer activeren zodra er betaald is
            if ($gebruiker->isSuspended()) {
                $gebruiker->UnSuspend();
            }
        }

        // Bon aanmaken als er nog geen is (genereer maakt alleen een bon bij status betaald)
        $nieuweBon = null;
        if (!$betaling->bon) {
            $nieuweBon = BonController::genereer(
                $betaling,
                $gebruiker->naam,
                $datum->translatedFormat('F Y')
            );
        }

        // Admins op de hoogte brengen
        $admins = Gebruiker::whereHas('rollen', function ($q) {
            $q->whereIn('naam', ['Administratie Medewerker', 'Applicatie Beheerder']);
        })->get();

        foreach ($admins as $admin) {
            Notificatie::create([
                'gebruiker_id' => $admin->gebruiker_id,
                'lid_id'       => $lid->lid_id,
                'Notif_type'   => 'Betaling_ingediend',
                'titel'        => 'Een nieuwe betaling is geregistreerd voor lid ' . $gebruiker->naam . ' op ' . $datum->format('Y-m-d') . '.',
                'gelezen'      => false,
                'gestuurd_op'  => now(),
            ]);
        }

        if (auth()->check()) {
            Activiteit::log(auth()->id(), 'betaling_geregistreerd', [
                'betaling_id' => $betaling->betaling_id,
                'lid_naam'    => $gebruiker->naam,
                'bedrag'      => $bedrag,
                'methode'     => $request->methode,
                'status'      => $request->status,
                'details'     => 'Betaling van SRD ' . $bedrag . ' geregistreerd voor lid ' . $gebruiker->naam . '.',
            ]);

            // Alleen loggen als er echt een bon is aangemaakt
            if ($nieuweBon) {
                Activiteit::log(auth()->id(), 'bon_aangemaakt', [
                    'betaling_id' => $betaling->betaling_id,
                    'lid_naam'    => $gebruiker->naam,
                    'details'     => 'Factuurbon automatisch aangemaakt voor betaling #' . $betaling->betaling_id . '.',
                ]);
            }
        }

        return response()->json(['success' => true, 'message' => 'Betaling succesvol toegevoegd'], 201);
    }

    // Betaling bijwerken
    public function update(Request $request, Betaling $betaling)
    {
        Gate::authorize('betalingen-beheren');

        $request->validate([
            'bedrag'          => 'required|numeric|min:150',
            'methode'         => 'required|in:fysiek,overmaking',
            'status'          => 'required|in:Openstaand,in_afwachting,betaald,niet_betaald',
            'datum'           => 'required|date',
            'betaling_bewijs' => 'nullable|pdf|max:5120',
        ]);

        // Alle maanden die bij deze betaling horen (1 bewijs = meerdere maanden).
        // Voor een losse betaling is dit gewoon de betaling zelf.
        $batch  = $this->batchVanBetaling($betaling);
        $aantal = $batch->count();

        // Oud (gedeeld) bewijs weggooien bij nieuwe upload
        if ($request->hasFile('betaling_bewijs') && $betaling->betaling_bewijs) {
            Storage::disk(config('filesystems.bewijs_disk'))->delete($betaling->betaling_bewijs);
        }

        // Bestaand pad behouden als er geen nieuwe upload is
        $bewijsPath = $betaling->betaling_bewijs;
        if ($request->hasFile('betaling_bewijs')) {
            $file = $request->file('betaling_bewijs');

            $bestandsnaam = Str::slug($betaling->lid->gebruiker->naam) //zet naam om naar kleine letters en koppel aan elkaar
            . '-lid-' . $betaling->lid_id                   //zet lid-id aan elkaar
            . '-' . now()->format('Ymd-His')                //"-20260707-1530-45"
            . '-betalingsbewijs'                            //zet betalingsbewijs aan elkaar
            . '.' . $file->getClientOriginalExtension();     //pdf

            $bewijsPath = $file->storeAs('bewijzen', $bestandsnaam, config('filesystems.bewijs_disk'));
        }

        $datum = Carbon::parse($request->datum);

        // Het ingevoerde bedrag is het totaal van de hele betaling. Verdeel het
        // gelijk over de maanden; een eventueel afrondingsverschil komt op de 1e maand.
        $bedragPerMaand = round($request->bedrag / $aantal, 2);
        $rest           = round($request->bedrag - ($bedragPerMaand * $aantal), 2);

        foreach ($batch->values() as $i => $b) {
            $b->bedrag          = $bedragPerMaand + ($i === 0 ? $rest : 0);
            $b->methode         = $request->methode;
            $b->status          = $request->status;
            $b->ingediend_op    = $datum->format('Y-m-d');
            $b->betaling_bewijs = $bewijsPath;

            // Alleen bij een losse betaling de maand/jaar meeschuiven met de datum;
            // bij een batch blijven de oorspronkelijke maanden behouden.
            if ($aantal === 1) {
                $b->maand = $datum->month;
                $b->jaar  = $datum->year;
            }

            $b->save();

            // Deadline updaten
            if (in_array($request->status, ['betaald', 'goed_gekeurd'])) {
                $b->berekenVolgendeDeadline($datum->format('Y-m-d'));
            } else {
                // Status niet meer betaald = deadline weghalen
                $b->volgende_deadline = null;
                $b->save();
            }
        }

        // Geschorst lid weer activeren zodra er betaald is
        if (in_array($request->status, ['betaald', 'goed_gekeurd'])) {
            $lidGebruiker = $betaling->lid ? $betaling->lid->gebruiker : null;
            if ($lidGebruiker && $lidGebruiker->isSuspended()) {
                $lidGebruiker->UnSuspend();
            }
        }

        if (auth()->check()) {
            Activiteit::log(auth()->id(), 'betaling_bijgewerkt', [
                'betaling_id' => $betaling->betaling_id,
                'details'     => $aantal . ' betaling(en) bijgewerkt (#' . $betaling->betaling_id . ').',
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Betaling succesvol bijgewerkt']);
    }

    // Soft delete
    public function destroy($betaling_id)
    {
        Gate::authorize('betalingen-beheren');

        $betaling = Betaling::find($betaling_id);

        if (!$betaling) {
            return redirect()->back()->with('error', 'Betaling niet gevonden');
        }

        // Bij een batch (1 bewijs, meerdere maanden) alles in 1 keer verwijderen
        $batch  = $this->batchVanBetaling($betaling);
        $aantal = $batch->count();

        foreach ($batch as $b) {
            $b->delete();
        }

        if (auth()->check()) {
            Activiteit::log(auth()->id(), 'betaling_verwijderd', [
                'betaling_id' => $betaling->betaling_id,
                'details'     => $aantal . ' betaling(en) (#'. $betaling->betaling_id . ', '. $betaling->ingediend_op .') van lid: ' . $betaling->lid->gebruiker->naam . ' verwijderd door '. auth()->user()->naam,
            ]);
        }

        return redirect()->back()->with('success', $aantal . ' betaling(en) verwijderd');
    }

    // Alle maanden die hetzelfde bewijs-bestand delen (1 betaling/upload), ongeacht status.
    // Voor een betaling zonder bewijs (handmatig) is dit gewoon de betaling zelf.
    private function batchVanBetaling(Betaling $betaling)
    {
        if (!$betaling->betaling_bewijs) {
            return collect([$betaling]);
        }

        return Betaling::where('lid_id', $betaling->lid_id)
            ->where('betaling_bewijs', $betaling->betaling_bewijs)
            ->get();
    }

    // Prullenbak pagina
    public function trashed()
    {
        Gate::authorize('betalingen-verwijderen');

        // onlyTrashed = alleen verwijderde
        $verwijderdeBetalingen = Betaling::onlyTrashed()
            ->Select('betalingen.*', 'gebruikers.naam')
            ->join('leden', 'leden.lid_id', '=', 'betalingen.lid_id')
            ->join('gebruikers', 'gebruikers.gebruiker_id', '=', 'leden.gebruiker_id')
            ->orderBy('betalingen.deleted_at', 'desc')
            ->get();

        return view('DeletedRecords', compact('verwijderdeBetalingen'));
    }

    // Betaling terughalen uit prullenbak
    public function restore($betaling_id)
    {
        Gate::authorize('betalingen-verwijderen');

        // withTrashed nodig, anders vindt Laravel hem niet
        $betaling = Betaling::withTrashed()->find($betaling_id);

        if (!$betaling) {
            return response()->json(['success' => false, 'message' => 'Betaling niet gevonden']);
        }

        // Voorkom dubbelen: er kan intussen een nieuwe betaling voor dezelfde maand zijn aangemaakt
        $bestaatAl = Betaling::where('lid_id', $betaling->lid_id)
            ->where('maand', $betaling->maand)
            ->where('jaar', $betaling->jaar)
            ->exists();

        if ($bestaatAl) {
            return response()->json([
                'success' => false,
                'message' => 'Er bestaat al een betaling voor deze maand. Verwijder die eerst voordat je deze herstelt.',
            ]);
        }

        $betaling->restore();

        if (auth()->check()) {
            Activiteit::log(auth()->id(), 'betaling_hersteld', [
                'betaling_id' => $betaling->betaling_id,
                'details'     => 'Betaling #' . $betaling->betaling_id . '('. $betaling->ingediend_op .') van lid: ' . $betaling->lid->gebruiker->naam . ' hersteld door '. auth()->user()->naam,
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Betaling succesvol hersteld']);
    }

    // Bewijs overzicht pagina
    public function showBewijsReceived(Request $request)
    {
        Gate::authorize('betalingen-beheren');

        // In afwachting van beoordeling
        $pendingPayments = Betaling::where('status', 'in_afwachting')
            ->with(['lid.gebruiker'])
            ->orderBy('ingediend_op', 'desc')
            ->paginate(5); //

        // Per upload samengevoegd, zodat meerdere maanden uit 1 bewijs als 1 rij tonen
        $recentReviews = Betaling::select(
                'gebruikers.naam',
                'betalingen.lid_id',
                'betalingen.jaar',
                DB::raw('COUNT(*) as aantal'),
                DB::raw('SUM(betalingen.bedrag) as totaal'),
                DB::raw('MIN(betalingen.maand) as eerste_maand'),
                DB::raw('MAX(betalingen.maand) as laatste_maand'),
                DB::raw('MAX(betalingen.ingediend_op) as ingediend_op'),
                DB::raw('MAX(betalingen.betaling_id) as betaling_id'),
                DB::raw('MAX(betalingen.status) as status')
            )
            ->join('leden', 'leden.lid_id', '=', 'betalingen.lid_id')
            ->join('gebruikers', 'gebruikers.gebruiker_id', '=', 'leden.gebruiker_id')
            ->whereIn('betalingen.status', ['betaald', 'goed_gekeurd', 'niet_goedgekeurd', 'Openstaand'])
            ->whereNotNull('betalingen.betaling_bewijs')
            ->groupBy('betalingen.betaling_bewijs', 'betalingen.lid_id', 'gebruikers.naam', 'betalingen.jaar')
            ->orderByDesc('ingediend_op')
            ->paginate(5, ['*'], 'recent_page');

        $pendingCount = Betaling::where('status', 'in_afwachting')->count();

        // Beoordeeld vandaag = acties van vandaag uit activiteitenlog
        $totalReviewedToday = Activiteit::whereIn('actie', ['betaling_goedgekeurd', 'betaling_afgewezen'])
            ->whereDate('aangemaakt_op', today())
            ->count();

        // Maanden uit 1 upload delen hetzelfde bewijs-bestand
        $batchInfo = [];
        Betaling::where('status', 'in_afwachting')
            ->whereNotNull('betaling_bewijs')
            ->orderBy('jaar')->orderBy('maand')
            ->get(['betaling_id', 'betaling_bewijs'])
            ->groupBy('betaling_bewijs')
            ->each(function ($groep) use (&$batchInfo) {
                $totaal = $groep->count();
                foreach ($groep->values() as $i => $b) {
                    $batchInfo[$b->betaling_id] = ['index' => $i + 1, 'totaal' => $totaal];
                }
            });

        // Lege placeholders zodat preview niet laadt bij opstarten
        $betaling = null;
        $bewijsUrl = null;
        $isPdf = false;

        return view('BewijsReceived', compact('pendingPayments', 'recentReviews', 'pendingCount', 'totalReviewedToday', 'betaling', 'bewijsUrl', 'isPdf', 'batchInfo'));
    }

    // Bewijs bekijken (PDF of afbeelding)
    public function ViewBewijsFile($betaling_id)
    {
        Gate::authorize('betalingen-beheren');

        $betaling = Betaling::findOrFail($betaling_id);

        // Check 1: is er een bewijs?
        if (!$betaling->betaling_bewijs) {
            return redirect()->back()->with('error', 'Geen betalingsbewijs gevonden voor deze betaling.');
        }

        // Check 2: bestaat het bestand nog op disk?
        if (!Storage::disk(config('filesystems.bewijs_disk'))->exists($betaling->betaling_bewijs)) {
            return redirect()->back()->with('error', 'Bestand niet gevonden op de server.');
        }

        // PDF of afbeelding bepalen
        $extensie = strtolower(pathinfo($betaling->betaling_bewijs, PATHINFO_EXTENSION));
        $isPdf    = $extensie === 'pdf';

        // Gebruik een streaming-route i.p.v. Storage::url():
        // Storage::url() geeft een pad-relatieve /storage/... URL terug en negeert
        // de /public subfolder op de live server (404). route() respecteert de
        // base path van het verzoek, dus dit werkt ook onder /public en heeft
        // geen storage:link symlink nodig.
        $bewijsUrl = route('StreamBewijsFile', $betaling->betaling_id);

        return view('BewijsReceived', compact('betaling', 'bewijsUrl', 'isPdf'));
    }

    // Stuurt het ruwe bewijsbestand (PDF/afbeelding) inline naar de browser
    public function StreamBewijsFile($betaling_id)
    {
        Gate::authorize('betalingen-beheren');

        $betaling = Betaling::findOrFail($betaling_id);

        if (!$betaling->betaling_bewijs || !Storage::disk(config('filesystems.bewijs_disk'))->exists($betaling->betaling_bewijs)) {
            abort(404, 'Bestand niet gevonden op de server.');
        }

        // Inline tonen (niet downloaden) zodat de iframe/img het kan weergeven.
        // Streamt vanaf de geconfigureerde disk; werkt ook voor cloud-opslag
        // (Supabase/S3) waar geen lokaal bestandspad bestaat.
        return Storage::disk(config('filesystems.bewijs_disk'))->response($betaling->betaling_bewijs);
    }

    // Bewijs goedkeuren (alle maanden uit dezelfde upload in 1 keer)
    public function ApproveBewijs(Request $request, $betaling_id)
    {
        $betaling = Betaling::findOrFail($betaling_id);
        $batch = $this->batchVanBewijs($betaling);

        foreach ($batch as $b) {
            $b->status  = 'betaald';
            $b->methode = 'overmaking';
            $b->save();
            $b->berekenVolgendeDeadline();

            // Bon per maand met de juiste periode (maand/jaar van de betaling zelf)
            if (!$b->bon) {
                $periode = \Carbon\Carbon::createFromDate($b->jaar, $b->maand, 1)->translatedFormat('F Y');
                BonController::genereer($b, $b->lid->gebruiker->naam, $periode);
            }
        }

        // Geschorst lid weer activeren nu het bewijs is goedgekeurd
        $lidGebruiker = $betaling->lid ? $betaling->lid->gebruiker : null;
        if ($lidGebruiker && $lidGebruiker->isSuspended()) {
            $lidGebruiker->UnSuspend();
        }

        $aantal = $batch->count();

        if (auth()->check()) {
            Activiteit::log(auth()->id(), 'betaling_goedgekeurd', [
                'betaling_id' => $betaling->betaling_id,
                'details'     => $aantal . ' betaling(en) goedgekeurd door '. auth()->user()->naam,
            ]);
        }

        // Notificatie naar het lid (alleen als hij een gekoppelde gebruiker heeft)
        if ($betaling->lid && $betaling->lid->gebruiker_id) {
            Notificatie::create([
                'gebruiker_id' => $betaling->lid->gebruiker_id,
                'lid_id'       => $betaling->lid_id,
                'Notif_type'   => 'betaling_goedgekeurd',
                'titel'        => 'Uw betaling voor ' . $aantal . ' maand(en) is goedgekeurd',
                'gelezen'      => 0,
                'gestuurd_op'  => now(),
            ]);
        }

        return redirect()->route('BewijsReceived')->with('success', $aantal . ' betaling(en) goedgekeurd');
    }

    // Bewijs afkeuren (alle maanden uit dezelfde upload in 1 keer)
    public function RejectBewijs(Request $request, $betaling_id)
    {
        $betaling = Betaling::findOrFail($betaling_id);
        $batch = $this->batchVanBewijs($betaling);

        foreach ($batch as $b) {
            $b->status = 'Openstaand';
            $b->betaling_bewijs = null;
            $b->save();
        }

        $aantal = $batch->count();

        if (auth()->check()) {
            Activiteit::log(auth()->id(), 'betaling_afgewezen', [
                'betaling_id' => $betaling->betaling_id,
                'details'     => $aantal . ' betaling(en) afgewezen door '. auth()->user()->naam,
            ]);
        }

        // Notificatie naar het lid (alleen als hij een gekoppelde gebruiker heeft)
        if ($betaling->lid && $betaling->lid->gebruiker_id) {
            Notificatie::create([
                'gebruiker_id' => $betaling->lid->gebruiker_id,
                'lid_id'       => $betaling->lid_id,
                'Notif_type'   => 'betaling_afgewezen',
                'titel'        => 'Uw betaling voor ' . $aantal . ' maand(en) is afgekeurd',
                'gelezen'      => 0,
                'gestuurd_op'  => now(),
            ]);
        }

        return redirect()->route('BewijsReceived')->with('error', $aantal . ' betaling(en) afgekeurd');
    }

    // Alle maanden in afwachting die hetzelfde bewijs-bestand delen (1 upload)
    private function batchVanBewijs(Betaling $betaling)
    {
        if (!$betaling->betaling_bewijs) {
            return collect([$betaling]);
        }

        return Betaling::where('lid_id', $betaling->lid_id)
            ->where('betaling_bewijs', $betaling->betaling_bewijs)
            ->where('status', 'in_afwachting')
            ->get();
    }

    // Dubbele openstaande betalingen opruimen
    public function removeduplicateBetalingen()
    {
        // Alleen openstaande zonder methode
        $allebetalingen = Betaling::with('bon')
            ->where('status', 'Openstaand')
            ->whereNull('methode')
            ->get();

        // Groep per lid + maand + jaar
        $gegroepeerd = $allebetalingen->groupBy(function ($betaling) {
            return $betaling->lid_id . '-' . $betaling->maand . '-' . $betaling->jaar;
        });

        $verwijderd = 0;

        foreach ($gegroepeerd as $groep) {
            if ($groep->count() > 1) {
                // Eerste behouden, rest weg
                foreach ($groep as $index => $betaling) {
                    if ($index === 0) continue;

                    $betaling->delete();
                    $verwijderd++;
                }
            }
        }

        return redirect()->back()->with('success', $verwijderd . ' dubbele betaling(en) verwijderd');
    }
}
