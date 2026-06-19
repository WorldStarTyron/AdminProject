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
                'bedrag'              => $lid->MaandelijkseBijdrage(),
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
            ->take(5)->get();

        // Totaal betaald deze maand (voor stats card)
        // Includes both 'betaald' and 'goed_gekeurd' as paid income
        $maandTotaal = Betaling::whereIn('status', ['betaald', 'goed_gekeurd'])
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


    // Bedrag per weekdag grafiek
   public function chartData(Request $request)
{
    $maand = (int) $request->input('maand', now()->month);
    $jaar  = (int) $request->input('jaar',  now()->year);

    $startDatum = Carbon::createFromDate($jaar, $maand, 1)->startOfMonth();
    $eindDatum  = $startDatum->copy()->endOfMonth();

    // Groepeer op kalenderdag
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

    for ($dag = 1; $dag <= $aantalDagen; $dag++) {
        $labels[] = (string) $dag;
        $data[]   = (float) ($betalingen->get($dag)->totaal_bedrag ?? 0);
    }

    return response()->json([
        'labels' => $labels,
        'series' => [
            [
                'name' => 'Totaal Bedrag',
                'data' => $data,  // was $bedragen
            ]
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
            'bedrag'          => 'required|numeric|min:150', //alleen betaling van 150
            'betaling_bewijs' => 'nullable|file|max:5120',
        ],[
            'bedrag.min' => 'Het bedrag moet minimaal SRD150 zijn',
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

        // Gebruik het ingevoerde bedrag (minimaal 150, gevalideerd hierboven)
        $bedrag = $request->bedrag;

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

        // Bereken volgende deadline als de betaling als betaald is geregistreerd
        if (in_array($request->status, ['betaald', 'goed_gekeurd'])) {
            $betaling->berekenVolgendeDeadline($datum->format('Y-m-d'));
        }

        // Bon aanmaken als er nog geen bon bestaat
        if (!$betaling->bon) {
            BonController::genereer(
                $betaling,
                $gebruiker->naam,
                $datum->translatedFormat('F Y')
            );
        }

        $admins = Gebruiker::whereHas('rollen', function($q) {
            $q->whereIn('naam', ['Administratie Medewerker', 'Applicatie Beheerder']);
        })->get();

        foreach ($admins as $admin) {
            Notificatie::create([
             'gebruiker_id' => $admin->gebruiker_id,
             'lid_id' => $lid->lid_id,
             'Notif_type' => 'Betaling_ingediend',
             'titel' => 'Een nieuwe betaling is geregistreerd voor lid ' . $gebruiker->naam . ' op ' . $datum->format('Y-m-d') . '.',
             'gelezen' => false,
             'gestuurd_op' => now(),
            ]);
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
            'bedrag'          => 'required|numeric|min:150', //alleen betaling van 100
            'methode'         => 'required|in:fysiek,overmaking',
            'status'          => 'required|in:Openstaand,in_wachting,afgewezen,betaald,niet_betaald',
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

        // Bereken volgende deadline bij statuswijziging
        if (in_array($request->status, ['betaald', 'goed_gekeurd'])) {
            $betaling->berekenVolgendeDeadline($datum->format('Y-m-d'));
        } else {
            // Status is niet meer betaald → deadline verwijderen
            $betaling->update(['volgende_deadline' => null]);
        }

        // Activiteit loggen
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

        // Activiteit loggen
        if (auth()->check()) {
            Activiteit::log(auth()->id(), 'betaling_verwijderd', [
                'betaling_id' => $betaling->betaling_id,
                'details'     => 'Betaling #'. $betaling->betaling_id . ' ('. $betaling->ingediend_op .') van lid: ' . $betaling->lid->gebruiker->naam . ' verwijderd door '. auth()->user()->naam,
            ]);
        }

        return redirect()->back()->with('success','Betaling verwijderd');
    }




    public function trashed()
    {
        Gate::authorize('betalingen-verwijderen');

        $verwijderdeBetalingen = Betaling::onlyTrashed()
        ->Select('betalingen.*','gebruikers.naam')
        ->join('leden','leden.lid_id', '=','betalingen.lid_id')
        ->join('gebruikers','gebruikers.gebruiker_id', '=','leden.gebruiker_id')
        ->orderBy('betalingen.deleted_at', 'desc')
        ->get();




        return view('DeletedRecords', compact('verwijderdeBetalingen'));
    }





    public function restore($betaling_id)
    {
        Gate::authorize('betalingen-verwijderen');

        //WithTrashed() zoekt ook in verwijderde rijen
        //zonder dit vindt laravel de betaling niet want die is weg

        $betaling = Betaling::withTrashed()->find($betaling_id);

        if(!$betaling){
            return response()->json(['success' => false, 'message' => 'Betaling niet gevonden']);
        }

        //restore() haalt de betaling terug uit de trash
        $betaling->restore();

        // Activiteit loggen
        if (auth()->check()) {
            Activiteit::log(auth()->id(), 'betaling_hersteld', [
                'betaling_id' => $betaling->betaling_id,
                'details'     => 'Betaling #' . $betaling->betaling_id . '('. $betaling->ingediend_op .') van lid: ' . $betaling->lid->gebruiker->naam . ' hersteld door '. auth()->user()->naam ,
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Betaling succesvol hersteld']);
    }






        // Betaal Bewijs Overzicht
  public function showBewijsReceived(Request $request)
  {
      Gate::authorize('betalingen-beheren'); // Administratie Medewerker + Applicatie Beheerder

      // Get actual pending payments (in afwachting)
      $pendingPayments = Betaling::where('status', 'in_afwachting')
          ->with(['lid.gebruiker'])
          ->orderBy('ingediend_op', 'desc')
          ->paginate(5);

      // Get recent reviews (status = betaald, goed_gekeurd, niet_goedgekeurd, or Openstaand which was rejected)
      $recentReviews = Betaling::whereIn('status', ['betaald', 'goed_gekeurd', 'niet_goedgekeurd', 'Openstaand'])
          ->whereNotNull('betaling_bewijs')
          ->with(['lid.gebruiker'])
          ->orderBy('ingediend_op', 'desc')
          ->take(5)
          ->get();

      // Stats
      $pendingCount = Betaling::where('status', 'in_afwachting')->count();

      $totalReviewedToday = Betaling::whereIn('status', ['betaald', 'goed_gekeurd', 'niet_goedgekeurd', 'Openstaand'])
          ->whereNotNull('betaling_bewijs')
          ->whereDate('ingediend_op', today())
          ->count();

          // Dit zijn de variabelen voor de pdf/afbeelding weergave. Zorgen ervoor dat de afbeelding niet wordt geladen bij opstarten
          $betaling = null;
          $bewijsUrl = null;
          $isPdf = false;

      return view('BewijsReceived', compact('pendingPayments', 'recentReviews', 'pendingCount', 'totalReviewedToday', 'betaling', 'bewijsUrl', 'isPdf'));
  }



// Betaal Bewijs Bekijken
  public function ViewBewijsFile($betaling_id)
{
    Gate::authorize('betalingen-beheren');

    // Find the payment or show a 404 page if it doesn't exist
    $betaling = Betaling::findOrFail($betaling_id);

    // Check 1: does this payment even have a proof file attached?
    if (!$betaling->betaling_bewijs) {
        return redirect()->back()->with('error', 'Geen betalingsbewijs gevonden voor deze betaling.');
    }

    // Check 2: does the file physically exist on disk?
    if (!\Storage::disk('public')->exists($betaling->betaling_bewijs)) {
        return redirect()->back()->with('error', 'Bestand niet gevonden op de server.');
    }

    // Build a public URL so the browser can display the file directly
    $bewijsUrl = \Storage::url($betaling->betaling_bewijs);

    // Detect whether the file is a PDF or an image so the view
    $extensie   = strtolower(pathinfo($betaling->betaling_bewijs, PATHINFO_EXTENSION));
    $isPdf      = $extensie === 'pdf';







    // PDF of Afbeelding weergeven
    $bewijsUrl = \Storage::url($betaling->betaling_bewijs);
    // Pass everything the view needs to the preview page
    return view('BewijsReceived', compact('betaling', 'bewijsUrl', 'isPdf'));
}


     // Betaal Bewijs Goed Keuren
    public function ApproveBewijs(Request $request, $betaling_id){
    $betaling = Betaling::findOrFail($betaling_id);
    $betaling->status = 'betaald';
    $betaling->methode = 'overmaking';
    $betaling->save();

    // Bereken volgende deadline na goedkeuring
    $betaling->berekenVolgendeDeadline();

    if(!$betaling->bon){
        $gebruiker = $betaling->lid->gebruiker;
        $datum  = \Carbon\Carbon::parse($betaling->ingediend_op);

        BonController::genereer(
            $betaling,
            $gebruiker->naam,
            $datum->translatedFormat('F Y')

        );
    }

    if(auth()->check()){
        Activiteit::log(auth()->id(), 'betaling_goedgekeurd', [
            'betaling_id' => $betaling->betaling_id,
            'details'     => 'Betaling #' . $betaling->betaling_id . ' goedgekeurd door '. auth()->user()->naam,
        ]);
    }


    return redirect()->back()->with('success', 'Betaling goedgekeurd');
}



public function RejectBewijs(Request $request, $betaling_id){
    $betaling = Betaling::findOrFail($betaling_id);
    $betaling->status = 'Openstaand';
    $betaling->save();

    // Log de activiteit (audit trail)
    if(auth()->check()){
        Activiteit::log(auth()->id(), 'betaling_afgewezen', [
            'betaling_id' => $betaling->betaling_id,
            
            'details'     => 'Betaling #' . $betaling->betaling_id . ' afgewezen door '. auth()->user()->naam,
        ]);
    }

    return redirect()->back()->with('success', 'Betaling afgekeurd');

}


// Verwijder dubbele betalingen
    // Dit zorgt ervoor dat als een lid al heeft betaald voor deze maand,
    // er geen nieuwe openstaande betaling wordt aangemaakt
    public function removeduplicateBetalingen()
    {
        // Haal alle betalingen op die:
        // Status is Openstaand
        // Een methode hebben (dus nog niet betaald)
        $allebetalingen = Betaling::with('bonnen')
            ->where('status', 'Openstaand')
            ->whereNull('methode')
            ->get();

        // Groepeer op lid_id + maand + jaar
        // Dan weten we welke betalingen voor dezelfde persoon en maand zijn
        $gegroepeerd = $allebetalingen->groupBy(function ($betaling) {
            return $betaling->lid_id . '-' . $betaling->maand . '-' . $betaling->jaar;
        });

        $verwijderd = 0;

        // Loop door elke groep
        foreach ($gegroepeerd as $groep) {
            // Als er meer dan 1 betaling is voor dezelfde maand
            if ($groep->count() > 1) {
                // Houd de eerste (oudste) betaling
                // Verwijder de rest
                $tehouden = $groep->first();

                foreach ($groep as $index => $betaling) {
                    // Skip de eerste, die houden we
                    if ($index === 0) continue;

                    // Verwijder de dubbele betaling
                    $betaling->delete();
                    $verwijderd++;
                }
            }
        }

        return redirect()->back()->with('success', $verwijderd . ' dubbele betaling(en) verwijderd');
    }
}
