<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Betaling;
use App\Models\Gebruiker;
use App\Models\Activiteit;
use App\Models\Lid;
use App\Http\Controllers\BonController;
use Carbon\Carbon;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage; // ← was missing, needed for deleting old files

class BetalingController extends Controller
{
    // Betalingen overzichtspagina
    public function index(Request $request)
    {
        Gate::authorize('betalingen-bekijken');

        // Huidige maand/jaar als standaard
        $maand = (int) $request->input('maand', now()->month);
        $jaar  = (int) $request->input('jaar', now()->year);

        // Haal alle leden op met hun gebruiker
        $leden = Lid::with('gebruiker')->get();

        $ledenStatus = [];
        foreach ($leden as $lid) {

            // Vind of maak een betalingsrecord aan voor deze maand/jaar
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

            // Update bedrag als het nog 0 is (oude situatie)
            if ($betaling->bedrag == 0 && $lid->MaandelijkseBijdrage() > 0) {
                $betaling->bedrag = $lid->MaandelijkseBijdrage();
                $betaling->save();
            }

            // Voeg lid toe aan de statuslijst
            $ledenStatus[] = (object) [
                'naam'                => $lid->gebruiker->naam ?? 'Onbekend',
                'status'              => $betaling->status,
                'lid_type'            => $lid->lid_type,
                'maandelijks_bijdrage'=> $lid->MaandelijkseBijdrage(),
                'betaling_id'         => $betaling->betaling_id,
            ];
        }

        // Recente betaalde betalingen (laatste 30 dagen)
        $recenteBetalingen = Betaling::select('betalingen.*', 'gebruikers.naam')
            ->join('leden',      'leden.lid_id',           '=', 'betalingen.lid_id')
            ->join('gebruikers', 'gebruikers.gebruiker_id', '=', 'leden.gebruiker_id')
            ->where('betalingen.status', 'betaald')
            ->where('betalingen.ingediend_op', '>=', now()->subDays(30))
            ->orderBy('betalingen.ingediend_op', 'desc')
            ->get(4);

        // Totaal betaald deze maand (voor stats card)
        $maandTotaal = Betaling::where('status', 'betaald')
            ->where('maand', $maand)
            ->where('jaar', $jaar)
            ->sum('bedrag');

        // Totaal onbetaald deze maand (voor stats card)
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

    // Grafiekdata voor de laatste 7 maanden (ApexCharts)
    public function chartData()
    {
        // Bouw lijst van laatste 7 maanden (oudste eerst)
        $maanden = collect(range(6, 0))->map(function ($maandenTerug) {
            $datum = now()->subMonths($maandenTerug);
            return [
                'jaar'  => (int) $datum->format('Y'),
                'maand' => (int) $datum->format('n'),
                'label' => $datum->translatedFormat('M'),
            ];
        });

        // Haal betaalde betalingen op per maand en methode
        $rijen = Betaling::selectRaw('jaar, maand, methode, COUNT(DISTINCT lid_id) as aantal')
            ->where('status', 'betaald')
            ->where(function ($q) use ($maanden) {
                foreach ($maanden as $m) {
                    $q->orWhere(function ($sub) use ($m) {
                        $sub->where('jaar', $m['jaar'])->where('maand', $m['maand']);
                    });
                }
            })
            ->groupBy('jaar', 'maand', 'methode')
            ->get()
            ->groupBy(fn($r) => $r->jaar . '-' . $r->maand)
            ->map(fn($groep) => $groep->keyBy('methode'));

        // Zet om naar ApexCharts series
        $labels     = [];
        $fysiek     = [];
        $overmaking = [];

        foreach ($maanden as $m) {
            $sleutel      = $m['jaar'] . '-' . $m['maand'];
            $labels[]     = $m['label'];
            $fysiek[]     = (int) ($rijen[$sleutel]['fysiek']->aantal     ?? 0);
            $overmaking[] = (int) ($rijen[$sleutel]['overmaking']->aantal ?? 0);
        }

        return response()->json([
            'labels' => $labels,
            'series' => [
                ['name' => 'Fysiek',     'data' => $fysiek],
                ['name' => 'Overmaking', 'data' => $overmaking],
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
            'status'          => 'required|in:Openstaand,betaald,niet_betaald',
            'bedrag'          => 'required|numeric|min:0',
            'betaling_bewijs' => 'nullable|file|max:5120',
        ]);

        // Zoek gebruiker op naam
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

        // Gebruik de verplichte maandelijkse bijdrage van het lid
        $bedrag = $lid->MaandelijkseBijdrage();

        // Bewijs opslaan als geüpload
        $bewijsPath = null;
        if ($request->hasFile('betaling_bewijs')) {
            $bewijsPath = $request->file('betaling_bewijs')->store('bewijzen', 'public');
        }

        // Maak of update betaling voor deze maand (voorkomt dubbele betalingen)
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

        // Bon aanmaken als er nog geen bon bestaat
        if (!$betaling->bon) {
            BonController::genereer(
                $betaling,
                $gebruiker->naam,
                $datum->translatedFormat('F Y')
            );
        }

        // Activiteit loggen
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

    // Bestaande betaling bijwerken
    public function update(Request $request, Betaling $betaling)
    {
        Gate::authorize('betalingen-beheren');

        $request->validate([
            'bedrag'          => 'required|numeric|min:0',
            'methode'         => 'required|in:fysiek,overmaking',
            'status'          => 'required|in:Openstaand,betaald,niet_betaald',
            'datum'           => 'required|date',
            'betaling_bewijs' => 'nullable|file|max:5120',
        ]);

        // Oud bewijs verwijderen als er een nieuw bestand is geüpload
        if ($request->hasFile('betaling_bewijs') && $betaling->betaling_bewijs) {
            Storage::disk('public')->delete($betaling->betaling_bewijs);
        }

        // Nieuw bewijs opslaan, anders oud pad behouden
        $bewijsPath = $betaling->betaling_bewijs;
        if ($request->hasFile('betaling_bewijs')) {
            $bewijsPath = $request->file('betaling_bewijs')->store('bewijzen', 'public');
        }

        $datum = Carbon::parse($request->datum);

        // Betaling bijwerken
        $betaling->update([
            'bedrag'          => $request->bedrag,
            'methode'         => $request->methode,
            'status'          => $request->status,
            'ingediend_op'    => $datum->format('Y-m-d'),
            'maand'           => $datum->month,
            'jaar'            => $datum->year,
            'betaling_bewijs' => $bewijsPath,
        ]);

        // Activiteit loggen
        if (auth()->check()) {
            Activiteit::log(auth()->id(), 'betaling_bijgewerkt', [
                'betaling_id' => $betaling->betaling_id,
                'details'     => 'Betaling #' . $betaling->betaling_id . ' bijgewerkt.',
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Betaling succesvol bijgewerkt']);
    }








    
    // Controleer of alle leden betaald hebben voor een bepaalde maand
    public function checkSubscriptie(Request $request)
    {
        $maand      = $request->input('maand', now()->month);
        $jaar       = $request->input('jaar', now()->year);
        $eindeMaand = Carbon::createFromDate($jaar, $maand, 1)->endOfMonth();
        $isVerlopen = now()->gt($eindeMaand);

        $leden       = Lid::with('gebruiker')->get();
        $nietBetaald = 0;
        $alBetaald   = 0;

        foreach ($leden as $lid) {
            $betaling = Betaling::where('lid_id', $lid->lid_id)
                ->where('maand', $maand)
                ->where('jaar', $jaar)
                ->first();

            // Lid heeft al betaald, sla over
            if ($betaling && $betaling->status === 'betaald') {
                $alBetaald++;
                continue;
            }

            if ($isVerlopen) {
                // Deadline verstreken → zet op niet_betaald
                if ($betaling) {
                    if ($betaling->status !== 'niet_betaald') {
                        $betaling->update(['status' => 'niet_betaald']);
                        $nietBetaald++;
                    }
                } else {
                    Betaling::create([
                        'lid_id'       => $lid->lid_id,
                        'bedrag'       => $lid->getRequiredMaandelijkseBijdrage(),
                        'status'       => 'niet_betaald',
                        'maand'        => $maand,
                        'jaar'         => $jaar,
                        'ingediend_op' => $eindeMaand->format('Y-m-d'),
                        'methode'      => null,
                    ]);
                    $nietBetaald++;
                }
            } else {
                // Deadline nog niet verstreken → zet op Openstaand
                if (!$betaling) {
                    Betaling::create([
                        'lid_id'       => $lid->lid_id,
                        'bedrag'       => $lid->getRequiredMaandelijkseBijdrage(),
                        'status'       => 'Openstaand',
                        'maand'        => $maand,
                        'jaar'         => $jaar,
                        'ingediend_op' => $eindeMaand->startOfMonth()->format('Y-m-d'),
                        'methode'      => null,
                    ]);
                    $nietBetaald++;
                } elseif ($betaling->status === 'niet_betaald') {
                    $nietBetaald++;
                } elseif ($betaling->status !== 'betaald') {
                    $betaling->update(['status' => 'Openstaand']);
                    $nietBetaald++;
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Check voltooid. $nietBetaald leden niet betaald / openstaand, $alBetaald leden betaald.",
        ]);
    }
}