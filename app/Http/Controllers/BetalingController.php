<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Betaling;
use App\Models\Gebruiker;  
use App\Models\Activiteit;
use App\Http\Controllers\BonController;
use Carbon\Carbon;
use App\Models\Lid;
use Illuminate\Support\Facades\Gate;

class BetalingController extends Controller
{


    
    public function index(Request $request)
    {
         Gate::authorize('betalingen-bekijken');

         // Bepaal geselecteerde maand/Jaar (default huidige maand)
         $maand = (int) $request->input('maand', now()->month);
         $jaar = (int) $request->input('jaar', now()->year);

         
         // Haal alle leden met hun gebruiker
         $leden = Lid::with('gebruiker')->get();

         $ledenStatus = [];
         foreach($leden as $lid){

            // vind of maak betalingsrecord noor deze maand/jaar
            $betaling = Betaling::firstOrCreate([
                'lid_id' => $lid->lid_id,
                'maand' => $maand,
                'jaar' => $jaar,
            ],
            [
                'status' => 'Openstaand',
                'bedrag' => $lid->MaandelijkseBijdrage(),
                'methode' => null,
                'ingediend_op' => Carbon::createFromDate($jaar, $maand, 1)->format('Y-m-d'),
                
            ]
        );

          // Als record al bestond maar nog geen bedrag heeft (bijv uit oude sitatautie, update dan)
        if($betaling->bedrag == 0 && $lid->MaandelijkseBijdrage() > 0){

            $betaling->bedrag = $lid->MaandelijkseBijdrage();
            $betaling->save();
        }
         
        // Maak een object van de lid status (eerste table)
        $ledenStatus[] = (object) [
            'naam' => $lid->gebruiker->naam ?? 'Onbekend',
            'status' => $betaling->status,
            'lid_type' => $lid->lid_type,
            'maandelijks_bijdrage' => $lid->MaandelijkseBijdrage(),
            'betaling_id' => $betaling->betaling_id,
           
            
        ];
    }

    //Recente betaalde betalingen (Tweede table)
    $recenteBetalingen = Betaling::select('betalingen.*', 'gebruikers.naam')
    ->join('leden' , 'leden.lid_id', '=', 'betalingen.lid_id')
    ->join('gebruikers' , 'gebruikers.gebruiker_id', '=', 'leden.gebruiker_id')
    ->where('betalingen.status', 'betaald')
    ->where('betalingen.ingediend_op', '>=', now()->subDays(30))
    ->orderBy('betalingen.ingediend_op', 'desc')
    ->get(4);

        // Totaal inkomsten huidige maand (voor de stats card)
        $maandTotaal = Betaling::where('status', 'betaald')
        ->where('maand', $maand)
        ->where('jaar', $jaar)
        ->sum('bedrag');

        // Onbetaald totaal (voor de stats card)
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
    Gate::authorize('betalingen-beheren');

    $request->validate([
        'naam'            => 'required|string',
        'datum'           => 'required|date',
        'methode'         => 'required|in:fysiek,overmaking',
        'status'          => 'required|in:Openstaand,betaald,niet_betaald',
        'bedrag'          => 'required|numeric|min:0',
        'betaling_bewijs' => 'nullable|file|max:5120',
    ]);

    // Zoek gebruiker op naam — geeft 404 als niet gevonden
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

    // Overschrijf bedrag met de verplichte maandelijkse bijdrage van het lid
    $bedrag = $lid->MaandelijkseBijdrage();

    // Bestand opslaan
    $bewijsPath = null;
    if ($request->hasFile('betaling_bewijs')) {
        $bewijsPath = $request->file('betaling_bewijs')->store('bewijzen', 'public');
    }

    // Vind of maak betaling voor deze maand (voorkomt dubbele betalingen)
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

    // Bon aanmaken (alleen als er nog geen bon bestaat)
    if (!$betaling->bon) {
        BonController::genereer(
            $betaling,
            $gebruiker->naam,
            $datum->translatedFormat('F Y')
        );
    }

    // Activiteiten loggen
    if (auth()->check()) {
        Activiteit::log(auth()->id(), 'betaling_geregistreerd', [
            'betaling_id' => $betaling->betaling_id,
            'lid_naam'    => $gebruiker->naam,
            'bedrag'      => $bedrag,
            'methode'     => $request->methode,
            'status'      => $request->status,
            'details'     => 'Betaling van SRD ' . $bedrag . ' geregistreerd voor lid ' . $gebruiker->naam . '.'
        ]);

        Activiteit::log(auth()->id(), 'bon_aangemaakt', [
            'betaling_id' => $betaling->betaling_id,
            'lid_naam'    => $gebruiker->naam,
            'details'     => 'Factuurbon automatisch aangemaakt voor betaling #' . $betaling->betaling_id . '.'
        ]);
    }

    return response()->json(['success' => true, 'message' => 'Betaling succesvol toegevoegd'], 201);
}
 







   // Handmatige subscriptie check - dit kan je vanuit de browser starten
   // Het checkt of alle leden betaald hebben voor de huidige maand
   public function checkSubscriptie(Request $request)
{
    $maand = $request->input('maand', now()->month);
    $jaar  = $request->input('jaar', now()->year);
    $eindeMaand = Carbon::createFromDate($jaar, $maand, 1)->endOfMonth();
    $isVerlopen = now()->gt($eindeMaand);

    $leden = Lid::with('gebruiker')->get();
    $nietBetaald = 0;
    $alBetaald = 0;

    foreach ($leden as $lid) {
        $betaling = Betaling::where('lid_id', $lid->lid_id)
            ->where('maand', $maand)
            ->where('jaar', $jaar)
            ->first();

        if ($betaling && $betaling->status === 'betaald') {
            $alBetaald++;
            continue;
        }

        if ($isVerlopen) {
            // Deadline verstreken -> niet betaald
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
            // Deadline nog niet verstreken -> zet op Openstaand (indien geen betaald)
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
                $nietBetaald++; // telt als nog niet betaald (openstaand)
            } elseif ($betaling->status === 'niet_betaald') {
                // Was al niet betaald, blijft zo
                $nietBetaald++;
            } elseif ($betaling->status !== 'betaald') {
                $betaling->update(['status' => 'Openstaand']);
                $nietBetaald++;
            }
        }
    }

    return response()->json([
        'success' => true,
        'message' => "Check voltooid. $nietBetaald leden niet betaald / openstaand, $alBetaald leden betaald."
    ]);
}







    

}