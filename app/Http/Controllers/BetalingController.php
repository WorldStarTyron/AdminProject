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

 
    // Bedrag per weekdag grafiek
    public function chartData(Request $request)
    {
        // filters van de frontend
        $maand = (int) $request->input('maand', now()->month);
        $jaar  = (int) $request->input('jaar',  now()->year);

        // Start- en einddatum van de geselecteerde maand
        $startDatum = Carbon::createFromDate($jaar, $maand, 1)->startOfMonth();
        $eindDatum  = $startDatum->copy()->endOfMonth();

        // Haal betaalde betalingen op binnen de geselecteerde maand
        $betalingen = Betaling::select(
            DB::raw('DAYOFWEEK(ingediend_op) as weekdag'),
            DB::raw('SUM(bedrag) as totaal')
        )
        ->where('status', 'betaald')
        ->whereBetween('ingediend_op', [$startDatum->toDateString(), $eindDatum->toDateString()])
        ->groupBy('weekdag')
        ->get()
        ->keyBy('weekdag');

        // DAYOFWEEK: 1=Zondag, 2=Maandag, ... 7=Zaterdag
        // Wij willen Ma(2) t/m Zo(1)
        $dagNamen = ['Ma', 'Di', 'Wo', 'Do', 'Vr', 'Za', 'Zo'];
        $dagKeys  = [2, 3, 4, 5, 6, 7, 1]; // MySQL DAYOFWEEK volgorde

        $labels   = [];
        $bedragen = [];

        for ($i = 0; $i < 7; $i++) {
            $labels[]   = $dagNamen[$i];
            $bedragen[] = (float) ($betalingen->get($dagKeys[$i])->totaal ?? 0);
        }

        return response()->json([
            'labels' => $labels,
            'series' => [
                [
                    'name' => 'Totaal Bedrag',
                    'data' => $bedragen,
                ]
            ]
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

  public function showBewijsRecieved(Request $request)
  {
      Gate::authorize('betalingen-verwijderen'); // Ensure admin access

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

      return view('BewijsRecieved', compact('pendingPayments', 'recentReviews', 'pendingCount', 'totalReviewedToday'));
  }




  public function DownloadBewijsFile($betaling_id)
  {
      Gate::authorize('betalingen-verwijderen');
      $betaling = Betaling::findOrFail($betaling_id);
      
      if (!$betaling->betaling_bewijs) {
          return redirect()->back()->with('error', 'Geen betalingsbewijs gevonden voor deze betaling.');
      }

      $filePath = storage_path('app/public/' . $betaling->betaling_bewijs);
      if (!file_exists($filePath)) {
          return redirect()->back()->with('error', 'Bestand bestaat niet meer.');
      }

      return response()->download($filePath);
  }





  
    



}