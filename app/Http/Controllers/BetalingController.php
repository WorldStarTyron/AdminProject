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

// Betalingen beheer
class BetalingController extends Controller
{
    // Betalingen overzichtspagina
    public function index(Request $request)
    {
        Gate::authorize('betalingen-bekijken');

        // Huidige maand/jaar als default
        $maand = (int) $request->input('maand', now()->month);
        $jaar  = (int) $request->input('jaar', now()->year);

        $leden = Lid::with('gebruiker')->get();

        $ledenStatus = [];
        foreach ($leden as $lid) {
            // Maakt automatisch een Openstaande betaling als er nog geen is
            $betaling = Betaling::firstOrCreate(
                [
                    'lid_id' => $lid->lid_id,
                    'maand'  => $maand,
                    'jaar'   => $jaar,
                ],
                [
                    'status'          => 'Openstaand',
                    'bedrag'          => $lid->MaandelijkseBijdrage(),
                    'methode'         => null,
                    'ingediend_op'    => Carbon::createFromDate($jaar, $maand, 1)->format('Y-m-d'),
                    'betaling_bewijs' => null,
                ]
            );

            // Bedrag aanvullen als het 0 was (oude records)
            if ($betaling->bedrag == 0 && $lid->MaandelijkseBijdrage() > 0) {
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

        // Laatste 5 betalingen van de afgelopen 30 dagen
        $recenteBetalingen = Betaling::select('betalingen.*', 'gebruikers.naam')
            ->join('leden',      'leden.lid_id',           '=', 'betalingen.lid_id')
            ->join('gebruikers', 'gebruikers.gebruiker_id', '=', 'leden.gebruiker_id')
            ->where('betalingen.status', 'betaald')
            ->where('betalingen.ingediend_op', '>=', now()->subDays(30))
            ->orderBy('betalingen.ingediend_op', 'desc')
            ->take(5)->get();

        // Totaal betaald deze maand
        $maandTotaal = Betaling::whereIn('status', ['betaald', 'goed_gekeurd'])
            ->where('maand', $maand)
            ->where('jaar', $jaar)
            ->sum('bedrag');

        // Totaal onbetaald deze maand
        $onbetaaldTotaal = Betaling::where('status', 'niet_betaald')
            ->where('maand', $maand)
            ->where('jaar', $jaar)
            ->sum('bedrag');

        return view('BetalingPagina', compact(
            'ledenStatus',
            'recenteBetalingen',
            'maandTotaal',
            'onbetaaldTotaal',
            'maand',
            'jaar'
        ));
    }

    // Bedrag per dag voor de grafiek
    public function chartData(Request $request)
    {
        $maand = (int) $request->input('maand', now()->month);
        $jaar  = (int) $request->input('jaar',  now()->year);

        $startDatum = Carbon::createFromDate($jaar, $maand, 1)->startOfMonth();
        $eindDatum  = $startDatum->copy()->endOfMonth();

        // Groep per dag
        $betalingen = Betaling::select(
            DB::raw('DAY(ingediend_op) as dag'),
            DB::raw('SUM(bedrag) as totaal_bedrag')
        )
        ->where('status', 'betaald')
        ->whereBetween('ingediend_op', [$startDatum->toDateString(), $eindDatum->toDateString()])
        ->groupBy('dag')
        ->get()
        ->keyBy('dag');

        $aantalDagen = $eindDatum->day;

        $labels = [];
        $data   = [];

        // Vul alle dagen, ook lege
        for ($dag = 1; $dag <= $aantalDagen; $dag++) {
            $labels[] = (string) $dag;
            $data[]   = (float) ($betalingen->get($dag)->totaal_bedrag ?? 0);
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
            'status'          => 'required|in:Openstaand,in_afwachting,afgewezen,betaald,niet_betaald',
            // Minimaal 150 (vaste contributie)
            'bedrag'          => 'required|numeric|min:150',
            'betaling_bewijs' => 'nullable|file|max:5120',
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

        // Bewijs opslaan als geupload
        $bewijsPath = null;
        if ($request->hasFile('betaling_bewijs')) {
            $bewijsPath = $request->file('betaling_bewijs')->store('bewijzen', 'public');
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
        }

        // Bon aanmaken als er nog geen is
        if (!$betaling->bon) {
            BonController::genereer(
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

            Activiteit::log(auth()->id(), 'bon_aangemaakt', [
                'betaling_id' => $betaling->betaling_id,
                'lid_naam'    => $gebruiker->naam,
                'details'     => 'Factuurbon automatisch aangemaakt voor betaling #' . $betaling->betaling_id . '.',
            ]);
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
            'status'          => 'required|in:Openstaand,in_wachting,afgewezen,betaald,niet_betaald',
            'datum'           => 'required|date',
            'betaling_bewijs' => 'nullable|file|max:5120',
        ]);

        // Oud bewijs weggooien bij nieuwe upload
        if ($request->hasFile('betaling_bewijs') && $betaling->betaling_bewijs) {
            Storage::disk('public')->delete($betaling->betaling_bewijs);
        }

        // Bestaand pad behouden als er geen nieuwe upload is
        $bewijsPath = $betaling->betaling_bewijs;
        if ($request->hasFile('betaling_bewijs')) {
            $bewijsPath = $request->file('betaling_bewijs')->store('bewijzen', 'public');
        }

        $datum = Carbon::parse($request->datum);

        $betaling->update([
            'bedrag'          => $request->bedrag,
            'methode'         => $request->methode,
            'status'          => $request->status,
            'ingediend_op'    => $datum->format('Y-m-d'),
            'maand'           => $datum->month,
            'jaar'            => $datum->year,
            'betaling_bewijs' => $bewijsPath,
        ]);

        // Deadline updaten
        if (in_array($request->status, ['betaald', 'goed_gekeurd'])) {
            $betaling->berekenVolgendeDeadline($datum->format('Y-m-d'));
        } else {
            // Status niet meer betaald = deadline weghalen
            $betaling->update(['volgende_deadline' => null]);
        }

        if (auth()->check()) {
            Activiteit::log(auth()->id(), 'betaling_bijgewerkt', [
                'betaling_id' => $betaling->betaling_id,
                'details'     => 'Betaling #' . $betaling->betaling_id . ' bijgewerkt.',
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
            return response()->json(['success' => false, 'message' => 'Betaling niet gevonden']);
        }

        $betaling->delete();

        if (auth()->check()) {
            Activiteit::log(auth()->id(), 'betaling_verwijderd', [
                'betaling_id' => $betaling->betaling_id,
                'details'     => 'Betaling #'. $betaling->betaling_id . ' ('. $betaling->ingediend_op .') van lid: ' . $betaling->lid->gebruiker->naam . ' verwijderd door '. auth()->user()->naam,
            ]);
        }

        return redirect()->back()->with('success', 'Betaling verwijderd');
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
            ->paginate(5);

        // Recent beoordeeld
        $recentReviews = Betaling::whereIn('status', ['betaald', 'goed_gekeurd', 'niet_goedgekeurd', 'Openstaand'])
            ->whereNotNull('betaling_bewijs')
            ->with(['lid.gebruiker'])
            ->orderBy('ingediend_op', 'desc')
            ->take(5)
            ->get();

        $pendingCount = Betaling::where('status', 'in_afwachting')->count();

        $totalReviewedToday = Betaling::whereIn('status', ['betaald', 'goed_gekeurd', 'niet_goedgekeurd', 'Openstaand'])
            ->whereNotNull('betaling_bewijs')
            ->whereDate('ingediend_op', today())
            ->count();

        // Lege placeholders zodat de preview niet laadt bij opstarten
        $betaling = null;
        $bewijsUrl = null;
        $isPdf = false;

        return view('BewijsReceived', compact('pendingPayments', 'recentReviews', 'pendingCount', 'totalReviewedToday', 'betaling', 'bewijsUrl', 'isPdf'));
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
        if (!\Storage::disk('public')->exists($betaling->betaling_bewijs)) {
            return redirect()->back()->with('error', 'Bestand niet gevonden op de server.');
        }

        $bewijsUrl = \Storage::url($betaling->betaling_bewijs);

        // PDF of afbeelding bepalen
        $extensie = strtolower(pathinfo($betaling->betaling_bewijs, PATHINFO_EXTENSION));
        $isPdf    = $extensie === 'pdf';

        $bewijsUrl = \Storage::url($betaling->betaling_bewijs);

        return view('BewijsReceived', compact('betaling', 'bewijsUrl', 'isPdf'));
    }

    // Bewijs goedkeuren
    public function ApproveBewijs(Request $request, $betaling_id)
    {
        $betaling = Betaling::findOrFail($betaling_id);
        $betaling->status = 'betaald';
        $betaling->methode = 'overmaking';
        $betaling->save();

        // Volgende deadline berekenen
        $betaling->berekenVolgendeDeadline();

        if (!$betaling->bon) {
            $gebruiker = $betaling->lid->gebruiker;
            $datum     = \Carbon\Carbon::parse($betaling->ingediend_op);

            BonController::genereer(
                $betaling,
                $gebruiker->naam,
                $datum->translatedFormat('F Y')
            );
        }

        if (auth()->check()) {
            Activiteit::log(auth()->id(), 'betaling_goedgekeurd', [
                'betaling_id' => $betaling->betaling_id,
                'details'     => 'Betaling #' . $betaling->betaling_id . ' goedgekeurd door '. auth()->user()->naam,
            ]);
        }

        return redirect()->back()->with('success', 'Betaling goedgekeurd');
    }

    // Bewijs afkeuren
    public function RejectBewijs(Request $request, $betaling_id)
    {
        $betaling = Betaling::findOrFail($betaling_id);
        $betaling->status = 'Openstaand';
        $betaling->save();

        if (auth()->check()) {
            Activiteit::log(auth()->id(), 'betaling_afgewezen', [
                'betaling_id' => $betaling->betaling_id,
                'details'     => 'Betaling #' . $betaling->betaling_id . ' afgewezen door '. auth()->user()->naam,
            ]);
        }

        return redirect()->back()->with('error', 'Betaling afgekeurd');
    }

    // Dubbele openstaande betalingen opruimen
    public function removeduplicateBetalingen()
    {
        // Alleen openstaande zonder methode
        $allebetalingen = Betaling::with('bonnen')
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
