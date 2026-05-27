<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Betaling;
use App\Models\Gebruiker;  
use App\Http\Controllers\BonController;
use Carbon\Carbon;

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
   $Betaling = Betaling::where('betaling_id', $betaling_id)->first();
   $Betaling->delete();  
   $Bon = Bon::where('betaling_id', $betaling_id)->first();
   $Bon->delete();
    return response()->json(['success' => true, 'message' => 'Betaling succesvol verwijderd']);
   }

}