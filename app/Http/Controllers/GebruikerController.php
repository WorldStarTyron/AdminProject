<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Gebruiker;
use App\Models\Lid;
use App\Models\Rol;
use App\Models\Activiteit;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;

// Gebruikers beheer pagina
class GebruikerController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('gebruikersbeheer');

        $zoek   = $request->input('zoek');
        $status = $request->input('status');
        $rol    = $request->input('rol');

        // when() = filter alleen toepassen als de waarde gevuld is
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

        // Tellers voor KPI kaarten
        $totaalGebruikers  = Gebruiker::count();
        $totaalAdmins      = Gebruiker::whereHas('rollen', fn($q) => $q->where('naam', 'Administratie Medewerker'))->count();
        $totaalVoorzitters = Gebruiker::whereHas('rollen', fn($q) => $q->where('naam', 'Voorzitter'))->count();

        $rollen = Rol::all();

        // Voor "geen resultaten" melding
        $geenResultaten = $gebruikers->isEmpty() && ($zoek || $status || $rol);

        return view('GebruikersBeheerPagina', compact(
            'gebruikers', 'totaalGebruikers', 'totaalAdmins', 'totaalVoorzitters', 'rollen', 'geenResultaten'
        ));
    }

    public function create()
    {
        Gate::authorize('Layouts.AddModals.AddGebruikerModal');
    }

    // Nieuwe gebruiker aanmaken
    public function store(Request $request)
    {
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

    // Gebruiker bijwerken
    public function update(Request $request, $id)
    {
        Gate::authorize('gebruikersbeheer');

        // Eigen record uitsluiten van unique check
        $validated = $request->validate([
            'naam'          => 'required|string|max:100',
            'email'         => 'required|email|max:150|unique:gebruikers,email,' . $id . ',gebruiker_id',
            'status'        => 'required|in:Actief,Inactief',
            'aangemaakt_op' => 'required|date',
        ]);

        $gebruiker = Gebruiker::findOrFail($id);
        $gebruiker->update($validated);

        return redirect()->back()->with('success', 'Account is geupdate');
    }

    // Gebruiker deactiveren
    public function deactiveer($gebruiker_id)
    {
        Gate::authorize('gebruikersbeheer');

        $gebruiker = Gebruiker::findOrFail($gebruiker_id);
        $gebruiker->status = 'Inactief';
        $gebruiker->save();

        return redirect()->back()->with('success', 'Account is gedeactiveerd');
    }

    // Gebruiker heractiveren
    public function heractiveer($gebruiker_id)
    {
        Gate::authorize('gebruikersbeheer');

        $gebruiker = Gebruiker::findOrFail($gebruiker_id);
        $gebruiker->status = 'Actief';
        $gebruiker->save();

        if (auth()->check()) {
            \App\Models\Activiteit::log(auth()->id(), 'lid_gewijzigd', [
                'lid_id'  => $gebruiker->gebruiker_id,
                'details' => 'Gebruiker ' . $gebruiker->naam . ' is succesvol hergeactiveerd.',
            ]);
        }

        return redirect()->route('GebruikersBeheer')->with('success', 'Account is succesvol hergeactiveerd.');
    }

    public function destroy($id)
    {
        //
    }
}
