<?php

namespace App\Http\Controllers;

use App\Models\Lid;
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
            'leden.naam',
            'leden.telefoonnummer',
            'leden.adres',
            'leden.email'
        )->get();
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
        $duplicatePerson = Lid::where('naam', $request->name)
            ->where('geboortedatum', $request->geboortedatum)
            ->exists();

        if ($duplicatePerson) {
            return response()->json([
                'errors' => [
                    'name' => ['Er bestaat al een lid met dezelfde naam en geboortedatum.']
                ]
            ], 422);
        }

        DB::transaction(function () use ($request) {
            // Voegt de gegevens toe aan de leden tabel
            Lid::create([
                'lid_id'         => (string) Str::uuid(),
                'naam'           => $request->name,
                'email'          => $request->email,
                'telefoonnummer' => $request->telefoonnummer,
                'adres'          => $request->adres,
                'woonplaats'     => $request->woonplaats,
                'geboortedatum'  => $request->geboortedatum,
            ]);
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
        $lid = Lid::where('lid_id', $id)->firstOrFail();

        $betalingen = \App\Models\Betaling::where('lid_id', $id)
            ->orderBy('betalingsdatum', 'desc')
            ->paginate(5);

        return view('EditLidPagina', compact('lid', 'betalingen'));
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

    // Remove the selected lid
    public function destroy(string $lidId)
    {
        $lid = Lid::where('lid_id', $lidId)->first();
        $lid->delete();
        return redirect()->route('ledenpagina')->with('success', 'Lid verwijderd');
    }
}