<?php

namespace App\Http\Controllers;

use App\Models\Lid;
use App\Models\Gebruiker;
use App\Http\Requests\StoreLidRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $leden = Lid::select(
            'leden.lid_id',
            'gebruikers.naam',
            'leden.telefoonnummer',
            'leden.adres',
            'leden.woonplaats',
            'gebruikers.email',
            'leden.lid_sinds'
        )->join('gebruikers', 'leden.gebruiker_id', '=', 'gebruikers.gebruiker_id')
        ->get();
        return view('ledenpagina', compact('leden'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('Add-Modal/add-lid-modal');
    }

    /**
     * Store a newly created resource in storage.
     *
     * VALIDATION: The StoreLidRequest class (in app/Http/Requests/)
     * automatically validates ALL fields before this code runs.
     * If validation fails, Laravel sends back error messages automatically.
     *
     * FIX: Removed gebruiker_id and betaalstatus from Lid::create()
     *      — deze kolommen bestaan niet meer in de leden tabel.
     *      User::create() is ook verwijderd want leden en gebruikers
     *      zijn nu losgekoppeld.
     */
    public function store(StoreLidRequest $request)
    {
        // Duplicate check: zelfde naam + geboortedatum = waarschijnlijk zelfde persoon
        $duplicatePerson = Gebruiker::where('naam', $request->name)->exists();

        if ($duplicatePerson) {
            return response()->json([
                'errors' => [
                    'name' => ['Er bestaat al een lid met dezelfde naam en geboortedatum.']
                ]
            ], 422);
        }

        DB::transaction(function () use ($request) {
            //1. Maak eerst een Lid account aan.
            $gebruiker = \App\Models\Gebruiker::create([
                'naam' => $request->name,
                'email' => $request->email,
                'wachtwoord_hash' => bcrypt('Welkom123'),
                'actief' => 1,
            ]);

              // 2.Voegt de gegevens toe aan de leden tabel
            Lid::create([
               'gebruiker_id' => $gebruiker->gebruiker_id,  // id van het nieuwe account
                'lid_type' => $request->lid_type,
                'telefoonnummer' => $request->telefoonnummer,
                'adres'          => $request->adres,
                'woonplaats'     => $request->woonplaats,
                'geboortedatum'  => $request->geboortedatum,
                'lid_sinds'      => $request->lid_sinds,
    
            ]);

            // 3. Ken de rol "Lid" toe aan de gebruiker
            $lidRol = \App\Models\Rol::where('naam', 'Lid')->first();
            if (!$lidRol) {
                $lidRol = \App\Models\Rol::create([
                    'naam' => 'Lid',
                    'omschrijving' => 'Standaard lidmaatschap',
                ]);
            }
            $gebruiker->rollen()->attach($lidRol->rol_id);
        });

        // Return JSON voor AJAX requests, redirect voor normale form posts
        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Lid toegevoegd'], 201);
        }

        return redirect()->route('ledenpagina')->with('success', 'Lid toegevoegd');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $lid = Lid::where('lid_id', $id)
        ->join('gebruikers', 'leden.gebruiker_id', '=', 'gebruikers.gebruiker_id')
        ->select('leden.*', 'gebruikers.naam', 'gebruikers.email')
        ->firstOrFail();

    $betalingen = \App\Models\Betaling::where('lid_id', $id)
        ->with('bon')
        ->orderBy('ingediend_op', 'desc')
        ->paginate(10);

    // Haal alle betaling_ids op van dit lid
    $betalingIds = \App\Models\Betaling::where('lid_id', $id)
        ->pluck('betaling_id');

    // Bonnen ophalen via de betaling_ids
    $bonnen = \App\Models\Bonnen::whereIn('bonnen.betaling_id', $betalingIds)
        ->join('betalingen', 'bonnen.betaling_id', '=', 'betalingen.betaling_id')
        ->join('leden', 'betalingen.lid_id', '=', 'leden.lid_id')
        ->join('gebruikers', 'leden.gebruiker_id', '=', 'gebruikers.gebruiker_id')
        ->orderBy('bonnen.betaling_id', 'desc')
        ->paginate(10);

    return view('LidOverzichtPagina', compact('lid', 'betalingen', 'bonnen'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $lid = Lid::where('lid_id', $id)
        ->join('gebruikers', 'leden.gebruiker_id', '=', 'gebruikers.gebruiker_id')
        ->select('leden.*', 'gebruikers.naam', 'gebruikers.email')
        ->firstOrFail();

          return view('EditLidPagina', compact('lid'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'naam' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'telefoonnummer' => 'required|string|max:255',
            'adres' => 'required|string|max:255',
            'woonplaats' => 'required|string|max:255',
            'geboortedatum' => 'required|date',
            'lid_sinds' => 'required|date',
            'lid_type' => 'required|string|max:255',
        ]);

        $lid = Lid::where('lid_id', $id)
        ->join('gebruikers', 'leden.gebruiker_id', '=', 'gebruikers.gebruiker_id')
        ->select('leden.*', 'gebruikers.naam', 'gebruikers.email')
        ->firstOrFail();

       

        //Update gebruikers tabel (naam + email)
        $gebruiker = Gebruiker::where('gebruiker_id', $lid->gebruiker_id)->update([
            'naam' => $validated['naam'],
            'email' => $validated['email'],
        ]);

       //update Leden table (De rest)
        $lid->update([
            'telefoonnummer' => $validated['telefoonnummer'],
            'adres'          => $validated['adres'],
            'woonplaats'     => $validated['woonplaats'],
            'geboortedatum'  => $validated['geboortedatum'],
            'lid_sinds'      => $validated['lid_sinds'],
            'lid_type'       => $validated['lid_type'],
        ]); 
        return redirect()->route('ledenpagina.show', $lid->lid_id)->with('success', 'Lid bewerkt');
        
    }

    // Remove the selected lid
    public function destroy(string $lidId)
    {
        $lid = Lid::where('lid_id', $lidId)->first();
        $lid->delete();
        return redirect()->route('ledenpagina')->with('success', 'Lid verwijderd');
    }
}