<?php

namespace App\Http\Controllers;

use App\Models\Lid;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

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
            'gebruikers.email'
        )->join('gebruikers','leden.gebruiker_id','=','gebruikers.gebruiker_id')
        ->get(); // haalt de gegevens op met een join op de gebruikers tabel
        return view('ledenpagina', compact('leden')); // stuurt de gegevens door naar de view
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('posts/LidToevoegen'); // stuurt de gegevens door naar the view
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:gebruikers,email',
            'telefoonnummer' => 'required|string|max:20',
            'woonplaats' => 'required|string|max:255',
            'adres' => 'required|string|max:255',
            'geboortedatum' => 'required|date',
        ]);

        \DB::transaction(function () use ($request) {
            $gebruiker_id = (string) Str::uuid();
            
            // voegt de gegevens toe aan de gebruikers tabel
            User::create([
                'gebruiker_id' => $gebruiker_id,
                'naam' => $request->name,
                'email' => $request->email,
                'wachtwoord_hash' => bcrypt('Welkom01!'), // Standaard wachtwoord
                'rol' => 'lid',
                'actief' => 1,
            ]);

            // voegt de gegevens toe aan de leden tabel
            Lid::create([
                'lid_id' => (string) Str::uuid(),
                'gebruiker_id' => $gebruiker_id,
                'telefoonnummer' => $request->telefoonnummer,
                'adres' => $request->adres,
                'woonplaats' => $request->woonplaats,
                'geboortedatum' => $request->geboortedatum,
                'betaalstatus' => 'Niet Betaald',
            ]);
        });

        // Return JSON for AJAX requests, redirect for regular form posts
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
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
};
