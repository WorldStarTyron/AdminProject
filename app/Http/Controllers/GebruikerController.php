<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Gebruiker;
use App\Models\Rol;
use App\Models\Activiteit;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;


class GebruikerController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('gebruikersbeheer'); 
        //Searchbare
        $zoek = $request->input('zoek');

        //Filters
        $status = $request->input('status');
        $rol = $request->input('rol');

        $gebruikers = Gebruiker::with('rollen')
        ->when($zoek, function($query) use ($zoek){
            $query->where('naam', 'like', "%{$zoek}%")
                  ->orWhere('email', 'like', "%{$zoek}%");
                  
        })
        ->when($status, function($query) use ($status){
            $query->where('status', $status);
        })
        ->when($rol, function($query) use ($rol){
            $query->whereHas('rollen', function($q) use ($rol){
                $q->where('naam', $rol);
            });
        })
        ->paginate(5)
        ->appends($request->query());

        
         // Count how many Admins, Voorzitters & gebruikers
        $totaalGebruikers = Gebruiker::count();
        $totaalAdmins = Gebruiker::whereHas('rollen', fn($q) => $q->where('naam', 'Administratie Medewerker'))->count();
        $totaalVoorzitters = Gebruiker::whereHas('rollen', fn($q) => $q->where('naam', 'Voorzitter'))->count();

        // Get all roles for filter dropdown
        $rollen = Rol::all();

        return view('GebruikersBeheerPagina', compact(
            'gebruikers', 'totaalGebruikers', 'totaalAdmins', 'totaalVoorzitters', 'rollen'));
    }   

    /**
     * Gebruiker updaten (naam, email, status).
     *
     * Deze functie valideert de invoergegevens, zoekt de specifieke gebruiker op,
     * werkt de record bij, logt de activiteit en stuurt de beheerder terug naar het overzicht.
     */
    public function update(Request $request, $id)
    {
        // Controleer of de gebruiker de bevoegdheid heeft om gebruikers te beheren
        Gate::authorize('gebruikersbeheer'); 

        // Valideer de inkomende naam, email en status.
        // Bij de unieke email check negeren we het ID van de huidige gebruiker, omdat ze hun eigen email mogen behouden.
        $validated = $request->validate([
            'naam'   => 'required|string|max:100',
            'email'  => 'required|email|max:150|unique:gebruikers,email,' . $id . ',gebruiker_id',
            'status' => 'required|in:Actief,Inactief',
        ]);

        // Zoek de gebruiker op via ID of geef een 404 fout als deze niet bestaat
        $gebruiker = Gebruiker::findOrFail($id);

        // Werk de gegevens bij in de database
        $gebruiker->update($validated);

        // Log de wijziging in de activiteitenlog
        if (auth()->check()) {
            Activiteit::log(auth()->id(), 'gebruiker_gewijzigd', [
                'gebruiker_id'   => $id,
                'gebruiker_naam' => $validated['naam'],
                'details'        => 'Gebruiker ' . auth()->user()->naam . ' heeft de gegevens van gebruiker ' . $validated['naam'] . ' bijgewerkt.'
            ]);
        }

        // Stuur de beheerder terug naar het overzicht met een succesmelding
        return redirect()->route('GebruikersBeheer')->with('success', 'Gebruiker succesvol bijgewerkt.');
    }

    /**
     * Gebruiker en bijbehorende gegevens (rollen, lidmaatschap en betalingen) verwijderen.
     *
     * Deze methode controleert de bevoegdheid, zorgt ervoor dat men zichzelf niet kan verwijderen,
     * en voert de verwijdering uit in een database-transactie om gegevensintegriteit te garanderen.
     */
    public function destroy($id)
    {
        // Controleer of de gebruiker de bevoegdheid heeft om gebruikers te beheren
        Gate::authorize('gebruikersbeheer');

        // Zoek de gebruiker op via ID of geef een 404 fout als deze niet bestaat
        $gebruiker = Gebruiker::findOrFail($id);

        // Beveiliging: zorg ervoor dat een ingelogde beheerder zichzelf niet kan verwijderen
        if ($gebruiker->gebruiker_id === auth()->id()) {
            return redirect()->back()->with('error', 'Je kunt jezelf niet verwijderen.');
        }

        $naam = $gebruiker->naam;

        // Voer de verwijdering uit binnen een transactie zodat alles of niets wordt verwijderd
        DB::transaction(function() use ($gebruiker) {
            // Ontkoppel eerst de rollen van deze gebruiker in de koppeltabel
            $gebruiker->rollen()->detach();

            // Als deze gebruiker ook een Lid (Member) profiel heeft, verwijder die dan ook
            if ($gebruiker->lid) {
                // Verwijder alle betalingen die bij dit lid horen
                $gebruiker->lid->betalingen()->delete();
                // Verwijder het lid record
                $gebruiker->lid->delete();
            }

            // Verwijder ten slotte het gebruiker record zelf
            $gebruiker->delete();
        });

        // Log de verwijdering in de activiteitenlog
        if (auth()->check()) {
            Activiteit::log(auth()->id(), 'gebruiker_verwijderd', [
                'gebruiker_naam' => $naam,
                'details'        => 'Gebruiker ' . auth()->user()->naam . ' heeft gebruiker ' . $naam . ' verwijderd.'
            ]);
        }

        // Stuur de beheerder terug naar het overzicht met een succesmelding
        return redirect()->route('GebruikersBeheer')->with('success', 'Gebruiker succesvol verwijderd.');
    }
}
