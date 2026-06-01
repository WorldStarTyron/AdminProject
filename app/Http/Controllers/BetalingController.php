<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Betaling;
use App\Models\Gebruiker;  
use App\Http\Controllers\BonController;
use Carbon\Carbon;
use App\Models\Bon;

class BetalingController extends Controller
{
    public function index(Request $request)
    {
        $betalingen = Betaling::select(
                'betalingen.*',
                'gebruikers.naam as gebruiker_naam',
            )
            ->join('leden', 'betalingen.lid_id', '=', 'leden.lid_id')
            ->join('gebruikers', 'leden.gebruiker_id', '=', 'gebruikers.gebruiker_id')
            ->with('bon')
            ->orderBy('betalingen.ingediend_op', 'desc')
            ->paginate(10);

        $maandTotaal = Betaling::where('status', 'betaald')
            ->whereMonth('ingediend_op', now()->month)
            ->whereYear('ingediend_op', now()->year)
            ->sum('bedrag');


            
             $query = Betaling::select(
            'betalingen.*',
            'gebruikers.naam as gebruiker_naam',
        )
        ->join('leden', 'betalingen.lid_id', '=', 'leden.lid_id')
        ->join('gebruikers', 'leden.gebruiker_id', '=', 'gebruikers.gebruiker_id')
        ->with('bon')
        ->orderBy('betalingen.ingediend_op', 'desc');

    // Filter op methode indien aanwezig
    if ($request->filled('methode')) {
        $query->where('betalingen.methode', $request->methode);
    }

    $betalingen = $query->paginate(10);

    $maandTotaal = Betaling::where('status', 'betaald')
        ->whereMonth('ingediend_op', now()->month)
        ->whereYear('ingediend_op', now()->year)
        ->sum('bedrag');

    return view('BetalingPagina', compact('betalingen', 'maandTotaal'));
    }

/**
     * Geeft per maand (laatste 7 maanden) het aantal betalende leden,
     * uitgesplitst per betaalmethode (fysiek / overmaking).
     * Alleen betalingen met status 'betaald' worden meegeteld.
     *
     * GET /betalingen/chart-data
     */
    public function chartData()
    {
        // Bouw een lijst van de laatste 7 maanden (oudste eerst)
        $maanden = collect(range(6, 0))->map(function ($maandenTerug) {
            $datum = now()->subMonths($maandenTerug);
            return [
                'jaar'  => (int) $datum->format('Y'),
                'maand' => (int) $datum->format('n'),
                'label' => $datum->translatedFormat('M'),   // Jan, Feb …
            ];
        });
 
        // Haal alle relevante betalingen op in één query
        $rijen = Betaling::selectRaw('jaar, maand, methode, COUNT(DISTINCT lid_id) as aantal')
            ->where('status', 'betaald')
            ->where(function ($q) use ($maanden) {
                // Filter op de 7 gewenste jaar-maand combinaties
                foreach ($maanden as $m) {
                    $q->orWhere(function ($sub) use ($m) {
                        $sub->where('jaar', $m['jaar'])
                            ->where('maand', $m['maand']);
                    });
                }
            })
            ->groupBy('jaar', 'maand', 'methode')
            ->get()
            ->groupBy(fn($r) => $r->jaar . '-' . $r->maand)  // sleutel: "2025-3"
            ->map(fn($groep) => $groep->keyBy('methode'));    // sleutel: "fysiek" / "overmaking"
 
        // Zet om naar series die ApexCharts verwacht
        $labels   = [];
        $fysiek   = [];
        $overmaking = [];
 
        foreach ($maanden as $m) {
            $sleutel = $m['jaar'] . '-' . $m['maand'];
            $labels[]     = $m['label'];
            $fysiek[]     = (int) ($rijen[$sleutel]['fysiek']->aantal     ?? 0);
            $overmaking[] = (int) ($rijen[$sleutel]['overmaking']->aantal ?? 0);
        }
 
        return response()->json([
            'labels'     => $labels,
            'series'     => [
                ['name' => 'Fysiek',     'data' => $fysiek],
                ['name' => 'Overmaking', 'data' => $overmaking],
            ],
        ]);
    }



    // Store Betalingen
    public function store(Request $request)
    {
        $request->validate([
            'naam'            => 'required|string',
            'datum'           => 'required|date',
            'methode'         => 'required|in:fysiek,overmaking',
            'status'          => 'required|in:in_behandeling,betaald,niet_betaald,afgewezen',
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

        $lid = $gebruiker->lid;

        // Bestand opslaan
        $bewijsPath = null;
        if ($request->hasFile('betaling_bewijs')) {
            $bewijsPath = $request->file('betaling_bewijs')->store('bewijzen', 'public');
        }

        $datum = Carbon::parse($request->datum);

        // Sla betaling op in variabele zodat we het kunnen doorgeven aan BonController
        $betaling = Betaling::create([
            'lid_id'          => $lid->lid_id,
            'bedrag'          => $request->bedrag,
            'methode'         => $request->methode,
            'status'          => $request->status,
            'maand'           => $datum->month,
            'jaar'            => $datum->year,
            'betaling_bewijs' => $bewijsPath,
        ]);

        // Maak een bon aan voor de gegeven betaling
        BonController::genereer(
            $betaling,
            $gebruiker->naam,
            $datum->translatedFormat('F Y')
        );

        return response()->json(['success' => true, 'message' => 'Betaling succesvol toegevoegd'], 201);
    }





    // Update Betalingen
   public function update(Request $request, $id){
    //Update Bonnen
    $betaling = Betaling::findOrFail($id);
    $oudStatus = $betaling->status;

    $betaling->update($request->only(['status', 'methode', 'bedrag']));

    // Als status verandert naar 'betaald' en er nog geen bon is
    if ($oudStatus !== 'betaald' && $betaling->status === 'betaald' && !$betaling->bon) {
        $gebruiker = $betaling->lid->gebruiker;
        $datum = Carbon::createFromDate($betaling->jaar, $betaling->maand, 1)
                       ->translatedFormat('F Y');

        BonController::genereer($betaling, $gebruiker->naam, $datum);
    }

    return response()->json(['success' => true, 'message' => 'Betaling bijgewerkt']);

   }
 

   // Delete Betalingen
   public function destroy($betaling_id)
   {
       $betaling = Betaling::where('betaling_id', $betaling_id)->first();

       if ($betaling) {
           // Check if bon exists and delete it
           $bon = Bon::where('betaling_id', $betaling_id)->first();
           if ($bon) {
               $bon->delete();
           }
           $betaling->delete();
       }

       return response()->json(['success' => true, 'message' => 'Betaling succesvol verwijderd']);
   }


   // Handmatige subscriptie check - dit kan je vanuit de browser starten
   // Het checkt of alle leden betaald hebben voor de huidige maand
   public function checkSubscriptie(Request $request)
   {
       // Welke maand en jaar checken we?
       $maand = $request->input('maand', now()->month);
       $jaar  = $request->input('jaar', now()->year);

       // Pak alle leden
       $leden = \App\Models\Lid::with('gebruiker')->get();

       // Tellers
       $nietBetaald = 0;
       $alBetaald   = 0;

       foreach ($leden as $lid) {

           // Check of dit lid al een betaling heeft voor deze maand
           $betaling = Betaling::where('lid_id', $lid->lid_id)
               ->where('maand', $maand)
               ->where('jaar', $jaar)
               ->first();

           // Als er al een betaling is met status "betaald" -> skip
           if ($betaling && $betaling->status === 'betaald') {
               $alBetaald++;
               continue;
           }

           // Als er al een "niet_betaald" record is -> skip
           if ($betaling && $betaling->status === 'niet_betaald') {
               continue;
           }

           // Als er een betaling is met andere status -> update naar niet_betaald
           if ($betaling) {
               $betaling->update(['status' => 'niet_betaald']);
               $nietBetaald++;
               continue;
           }

           // Geen betaling gevonden -> maak een "niet_betaald" record
           Betaling::create([
               'lid_id'  => $lid->lid_id,
               'bedrag'  => 0,
               'methode' => 'fysiek',
               'status'  => 'niet_betaald',
               'maand'   => $maand,
               'jaar'    => $jaar,
           ]);

           $nietBetaald++;
       }

       // Stuur resultaat terug
       return response()->json([
           'success' => true,
           'message' => "Check klaar! {$nietBetaald} leden als 'niet betaald' gemarkeerd, {$alBetaald} leden al betaald.",
           'niet_betaald' => $nietBetaald,
           'al_betaald'   => $alBetaald,
           'totaal'       => $leden->count(),
       ]);
       
   }


    //Rapportage Pagina Data
    public function rapportageData(Request $request)
    {
        // We zoeken de oudste en nieuwste betaling in de database
        $oudsteDatum = Betaling::min('ingediend_op');
        $nieuwsteDatum = Betaling::max('ingediend_op');

        // We pakken de datums die zijn gekozen, of gebruiken de oudste en nieuwste betaling
        $from = $request->input('from', $oudsteDatum ? date('Y-m-d', strtotime($oudsteDatum)) : date('Y-01-01'));
        $to = $request->input('to', $nieuwsteDatum ? date('Y-m-d', strtotime($nieuwsteDatum)) : date('Y-m-d'));

        // We maken de filterdatums inclusief voor het begin en einde van de dag
        $fromStart = $from . ' 00:00:00';
        $toEnd = $to . ' 23:59:59';

        // We filteren de betalingen op de gekozen datums
        $query = Betaling::query()->whereBetween('ingediend_op', [$fromStart, $toEnd]);

        // Card 1: Totale inkomsten (som van alle betaalde betalingen)
        $totaleInkomsten = (clone $query)->where('status', 'betaald')->sum('bedrag');

        // Card 2: Openstaand bedrag (som van niet-betaalde betalingen)
        $openstaandBedrag = (clone $query)->where('status', 'niet_betaald')->sum('bedrag');
        $openstaandAantal = (clone $query)->where('status', 'niet_betaald')->distinct('lid_id')->count('lid_id');

        // Als er wel openstaande betalingen zijn maar het geregistreerde bedrag is 0,
        // schatten we 150 SRD per lid
        if ($openstaandBedrag == 0 && $openstaandAantal > 0) {
            $openstaandBedrag = $openstaandAantal * 150;
        }

        // We berekenen het totale verwachte bedrag en het betaalpercentage (dekkingsgraad)
        $totaalVerwacht = $totaleInkomsten + $openstaandBedrag;
        $dekkingsgraad = $totaalVerwacht > 0 ? round(($totaleInkomsten / $totaalVerwacht) * 100, 1) : 100;

        // We berekenen de echte omzetverdeling per betaalmethode binnen de geselecteerde periode
        $overmakingBedrag = (clone $query)->where('status', 'betaald')->where('methode', 'overmaking')->sum('bedrag');
        $fysiekBedrag = (clone $query)->where('status', 'betaald')->where('methode', 'fysiek')->sum('bedrag');
        $totaalBedragMethoden = $overmakingBedrag + $fysiekBedrag;

        if ($totaalBedragMethoden > 0) {
            $overmakingPercentage = round(($overmakingBedrag / $totaalBedragMethoden) * 100);
            $fysiekPercentage = 100 - $overmakingPercentage;
        } else {
            $overmakingPercentage = 50;
            $fysiekPercentage = 50;
        }

        // Card 3: Totaal leden
        $totaalLeden = \App\Models\Lid::count();

        // Recente betalingen tabel (laatste 10)
        $betalingen = Betaling::select('betalingen.*', 'gebruikers.naam')
            ->join('leden', 'betalingen.lid_id', '=', 'leden.lid_id')
            ->join('gebruikers', 'leden.gebruiker_id', '=', 'gebruikers.gebruiker_id')
            ->whereBetween('betalingen.ingediend_op', [$fromStart, $toEnd])
            ->orderBy('betalingen.ingediend_op', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($b) {
                $b->datum = $b->ingediend_op;
                $b->status_label = match($b->status) {
                    'betaald'        => 'Betaald',
                    'niet_betaald'   => 'Niet betaald',
                    'in_behandeling' => 'In behandeling',
                    default          => ucfirst($b->status),
                };
                return $b;
            });

        // We maken de datums netjes voor de grafiek
        try {
            $fromCarbon = \Carbon\Carbon::parse($from)->startOfMonth();
            $toCarbon = \Carbon\Carbon::parse($to)->startOfMonth();
        } catch (\Exception $e) {
            $fromCarbon = now()->subMonths(6)->startOfMonth();
            $toCarbon = now()->startOfMonth();
        }

        // We bouwen een lijst met alle maanden die tussen de datums liggen
        $maanden = collect();
        if ($fromCarbon->lte($toCarbon)) {
            $huidige = $fromCarbon->copy();
            // Maximaal 12 maanden in de grafiek om het netjes te houden
            $maxMaanden = 12;
            while ($huidige->lte($toCarbon) && $maanden->count() < $maxMaanden) {
                $maanden->push([
                    'jaar'  => (int) $huidige->format('Y'),
                    'maand' => (int) $huidige->format('n'),
                    'label' => ucfirst($huidige->translatedFormat('M')), // Maandnaam zonder jaartal
                ]);
                $huidige->addMonth();
            }
        }

        // We halen de inkomsten op per maand en per betaalmethode
        $rijen = Betaling::selectRaw('jaar, maand, methode, SUM(bedrag) as totaal')
            ->where('status', 'betaald')
            ->whereBetween('ingediend_op', [$fromStart, $toEnd])
            ->groupBy('jaar', 'maand', 'methode')
            ->get()
            ->groupBy(fn($r) => $r->jaar . '-' . $r->maand)
            ->map(fn($groep) => $groep->keyBy('methode'));

        $chartLabels     = [];
        $chartFysiek     = [];
        $chartOvermaking = [];

        foreach ($maanden as $m) {
            $sleutel           = $m['jaar'] . '-' . $m['maand'];
            $chartLabels[]     = $m['label'];
            $chartFysiek[]     = (float) ($rijen[$sleutel]['fysiek']->totaal     ?? 0);
            $chartOvermaking[] = (float) ($rijen[$sleutel]['overmaking']->totaal ?? 0);
        }

        return view('RapportPagina', compact(
            'totaleInkomsten',
            'openstaandBedrag',
            'openstaandAantal',
            'totaalLeden',
            'betalingen',
            'from',
            'to',
            'dekkingsgraad',
            'totaalVerwacht',
            'overmakingPercentage',
            'fysiekPercentage',
            'chartLabels',
            'chartFysiek',
            'chartOvermaking'
        ));
    }

}