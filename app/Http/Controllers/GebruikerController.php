<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Gebruiker;
use App\Models\Lid;
use App\Models\Rol;
use App\Models\Activiteit;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;


class GebruikerController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('gebruikersbeheer');

        $zoek   = $request->input('zoek');
        $status = $request->input('status');
        $rol    = $request->input('rol');

        $gebruikers = Gebruiker::with('rollen')
            ->when($zoek, function ($query) use ($zoek) {
                $query->where('naam', 'like', "%{$zoek}%")
                      ->orWhere('email', 'like', "%{$zoek}%");
            })
            ->when($status, function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->when($rol, function ($query) use ($rol) {
                $query->whereHas('rollen', function ($q) use ($rol) {
                    $q->where('naam', $rol);
                });
            })
            ->paginate(5)
            ->appends($request->query());

        $totaalGebruikers  = Gebruiker::count();
        $totaalAdmins      = Gebruiker::whereHas('rollen', fn($q) => $q->where('naam', 'Administratie Medewerker'))->count();
        $totaalVoorzitters = Gebruiker::whereHas('rollen', fn($q) => $q->where('naam', 'Voorzitter'))->count();

        $rollen = Rol::all();

        // Controleer of er een zoekterm of filter actief is maar geen resultaten zijn gevonden.
        $geenResultaten = $gebruikers->isEmpty() && ($zoek || $status || $rol);

        return view('GebruikersBeheerPagina', compact(
            'gebruikers', 'totaalGebruikers', 'totaalAdmins', 'totaalVoorzitters', 'rollen', 'geenResultaten'
        ));
    }

    public function create()
    {
        // Dit is niet strikt nodig omdat je de formulierknop alleen toont bij de juiste rechten,
        // maar voor de zekerheid mag alleen een beheerder een pagina openen om te maken.
        Gate::authorize('Layouts.AddModals.AddGebruikerModal');
    }

    public function store(Request $request){

        $validated = $request->validate([
              'naam' => 'required|string|max:255',
              'email' => 'required|email|unique:gebruikers,email',
              'wachtwoord' => 'required|string|min:8|confirmed',
              
            
        ]);

         $gebruiker = Gebruiker::create([
        'naam' => $validated['naam'],
        'email' => $validated['email'],
        'wachtwoord_hash' => bcrypt($validated['wachtwoord']),
        'status' => 'Actief',
        'aangemaakt_op' => now(),
        'bijgewerkt_op' => now(),
    ]);




    //Activiteit
      if (auth()->check()) {
        Activiteit::log(auth()->id(), 'gebruiker_toegevoegd', [
            'gebruiker_id'  => $gebruiker->gebruiker_id,
            'details' => 'Account ' . $gebruiker->naam . ' succesvol aangemaakt.',
        ]);
    }

    return redirect()->route('GebruikersBeheer')->with('success', 'Gebruiker succesvol toegevoegd');
    }




    public function show(Request $request, $id)
    {
        Gate::authorize('gebruikersbeheer');
        $gebruiker = Gebruiker::with(['lid', 'rollen'])->findOrFail($id);

        return view('gebruikers.show', compact('gebruiker'));
    }


    /**
     * Gebruiker updaten (naam, email, status).
     *
     * Geeft JSON terug zodat de modal de tabelrij live kan bijwerken
     * zonder de pagina te herladen.
     */
    public function update(Request $request, $id)
{
    Gate::authorize('gebruikersbeheer');

    // Stap 1: Valideer de invoer
    $validated = $request->validate([
        'naam'          => 'required|string|max:100',
        'email'         => 'required|email|max:150|unique:gebruikers,email,' . $id . ',gebruiker_id',
        'status'        => 'required|in:Actief,Inactief',
        'aangemaakt_op' => 'required|date',
    ]);

    // Stap 2: Gebruiker ophalen & bijwerken
    $gebruiker = Gebruiker::findOrFail($id);
    $gebruiker->update($validated);

   return redirect()->back()->with('succes', 'Account is geupdate');

}


  // Gebruiker deactiveren
public function deactiveer($gebruiker_id)
{
    // Check of de ingelogde gebruiker dit mag (zelfde check als andere acties)
    Gate::authorize('gebruikersbeheer');

    $gebruiker = Gebruiker::findOrFail($gebruiker_id);
    $gebruiker->status = 'Inactief';
    $gebruiker->save();

    return redirect()->back()->with('succes', 'Acount is gedeactiveerd');
}

// Gebruiker heractiveren
public function heractiveer($gebruiker_id)
{
    // Check of de ingelogde gebruiker dit mag
    Gate::authorize('gebruikersbeheer');

    // Zoek de gebruiker op
    $gebruiker = Gebruiker::findOrFail($gebruiker_id);
    $gebruiker->status = 'Actief';
    $gebruiker->save();

    // Activiteit loggen (bestaande functionaliteit, ongewijzigd)
    if (auth()->check()) {
        \App\Models\Activiteit::log(auth()->id(), 'lid_gewijzigd', [
            'lid_id'  => $gebruiker->gebruiker_id,
            'details' => 'Gebruiker ' . $gebruiker->naam . ' is succesvol hergeactiveerd.',
        ]);
    }

    return redirect()->route('GebruikersBeheer')->with('succes', 'Account is succesvol hergeactiveerd.');
}

    /**
     * Gebruiker en bijbehorende gegevens verwijderen.
     */
    public function destroy($id)
    {
      //
    }




   
}