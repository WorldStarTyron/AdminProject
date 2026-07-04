<?php

namespace App\Http\Controllers;

use App\Models\Lid;
use App\Models\Gebruiker;
use App\Models\Activiteit;
use App\Http\Requests\StoreLidRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

// Lid CRUD
class PostController extends Controller
{
    // Nieuw lid toevoegen (met gebruiker, rol en eerste betaling)
    public function store(StoreLidRequest $request)
    {
        Gate::authorize('leden-beheren');

        // Check op zelfde naam + geboortedatum
        $duplicatePerson = Gebruiker::where('naam', $request->name)
            ->whereHas('lid', function ($q) use ($request) {
                $q->where('geboortedatum', $request->geboortedatum);
            })
            ->exists();

        if ($duplicatePerson) {
            return response()->json([
                'errors' => [
                    'name' => ['Er bestaat al een lid met dezelfde naam en geboortedatum.']
                ]
            ], 422);
        }

        try {
            // Alles in 1 transactie zodat er geen halve records overblijven
            DB::transaction(function () use ($request) {
                // 1. Gebruiker aanmaken met standaard wachtwoord
                $gebruiker = \App\Models\Gebruiker::create([
                    'naam'            => $request->name,
                    'email'           => $request->email,
                    'wachtwoord_hash' => bcrypt('Welkom123'),
                    'status'          => 'Actief',
                ]);

                // 2. Lid koppelen aan gebruiker
                Lid::create([
                    'gebruiker_id'  => $gebruiker->gebruiker_id,
                    'lid_type'      => $request->lid_type,
                    'telefoonnummer'=> $request->telefoonnummer,
                    'adres'         => $request->adres,
                    'woonplaats'    => $request->woonplaats,
                    'geboortedatum' => $request->geboortedatum,
                    'lid_sinds'     => $request->lid_sinds,
                ]);

                // 3. Rol "Lid" toekennen, anders aanmaken
                $lidRol = \App\Models\Rol::where('naam', 'Lid')->first();
                if (!$lidRol) {
                    $lidRol = \App\Models\Rol::create([
                        'naam'         => 'Lid',
                        'omschrijving' => 'Standaard lidmaatschap',
                    ]);
                }
                $gebruiker->rollen()->attach($lidRol->rol_id);

                // 4. Eerste openstaande betaling aanmaken
                $datum    = \Carbon\Carbon::now();
                $nieuwLid = \App\Models\Lid::where('gebruiker_id', $gebruiker->gebruiker_id)->firstOrFail();

                \App\Models\Betaling::create([
                    'lid_id'       => $nieuwLid->lid_id,
                    'bedrag'       => $nieuwLid->MaandelijkseBijdrage(),
                    'methode'      => 'fysiek',
                    'status'       => 'Openstaand',
                    'maand'        => $datum->month,
                    'jaar'         => $datum->year,
                    // ingediend_op mag niet null zijn in de db
                    'ingediend_op' => $datum,
                ]);

                // 5. Activiteit loggen
                if (auth()->check()) {
                    Activiteit::log(auth()->id(), 'lid_aangemaakt', [
                        'lid_naam'  => $request->name,
                        'lid_email' => $request->email,
                        'lid_type'  => $request->lid_type,
                        'details'   => 'Gebruiker ' . auth()->user()->naam . ' heeft een nieuw lid toegevoegd: ' . $request->name . '.'
                    ]);
                } else {
                    Activiteit::log($gebruiker->gebruiker_id, 'lid_aangemaakt', [
                        'lid_naam'  => $request->name,
                        'lid_email' => $request->email,
                        'lid_type'  => $request->lid_type,
                        'details'   => 'Gebruiker Systeem heeft een nieuw lid toegevoegd: ' . $request->name . '.'
                    ]);
                }
            });
        } catch (\Exception $e) {
            Log::error('Lid toevoegen mislukt: ' . $e->getMessage(), [
                'exception' => $e,
                'request'   => $request->all(),
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'errors' => ['server' => ['Er is een serverfout opgetreden: ' . $e->getMessage()]]
                ], 500);
            }

            return redirect()->back()->with('error', 'Er is een serverfout opgetreden. Probeer het opnieuw.');
        }

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Lid toegevoegd'], 201);
        }

        return redirect()->route('ledenpagina')->with('success', 'Lid toegevoegd');
    }

    // Lid detailpagina
    public function show(string $id)
    {
        Gate::authorize('leden-bekijken');

        $lid = Lid::where('lid_id', $id)
            ->join('gebruikers', 'leden.gebruiker_id', '=', 'gebruikers.gebruiker_id')
            ->select('leden.*', 'gebruikers.naam', 'gebruikers.email')
            ->firstOrFail();

        $betalingen = \App\Models\Betaling::where('lid_id', $id)
            ->with('bon')
            ->orderBy('ingediend_op', 'desc')
            ->paginate(10);

        $betalingIds = \App\Models\Betaling::where('lid_id', $id)->pluck('betaling_id');

        $bonnen = \App\Models\Bonnen::whereIn('bonnen.betaling_id', $betalingIds)
            ->join('betalingen', 'bonnen.betaling_id', '=', 'betalingen.betaling_id')
            ->join('leden', 'betalingen.lid_id', '=', 'leden.lid_id')
            ->join('gebruikers', 'leden.gebruiker_id', '=', 'gebruikers.gebruiker_id')
            ->orderBy('bonnen.betaling_id', 'desc')
            ->paginate(10);

        return view('LidOverzichtPagina', compact('lid', 'betalingen', 'bonnen'));
    }

    public function edit(string $id)
    {
        Gate::authorize('leden-beheren');

        $lid = Lid::where('lid_id', $id)
            ->join('gebruikers', 'leden.gebruiker_id', '=', 'gebruikers.gebruiker_id')
            ->select('leden.*', 'gebruikers.naam', 'gebruikers.email')
            ->firstOrFail();

        return view('editLidPagina', compact('lid'));
    }

    // Lid bewerken
    public function update(Request $request, string $id)
    {
        Gate::authorize('leden-beheren');

        $validated = $request->validate([
            'naam'           => 'required|string|max:255',
            'email'          => 'required|email|max:255',
            'telefoonnummer' => 'required|string|max:255',
            'adres'          => 'required|string|max:255',
            'woonplaats'     => 'required|string|max:255',
            'geboortedatum'  => 'required|date',
            'lid_sinds'      => 'required|date',
            'lid_type'       => 'required|string|max:255',
        ]);

        $lid = Lid::where('lid_id', $id)
            ->join('gebruikers', 'leden.gebruiker_id', '=', 'gebruikers.gebruiker_id')
            ->select('leden.*', 'gebruikers.naam', 'gebruikers.email')
            ->firstOrFail();

        // Naam en email zitten op gebruikers tabel
        Gebruiker::where('gebruiker_id', $lid->gebruiker_id)->update([
            'naam'  => $validated['naam'],
            'email' => $validated['email'],
        ]);

        $lid->update([
            'telefoonnummer' => $validated['telefoonnummer'],
            'adres'          => $validated['adres'],
            'woonplaats'     => $validated['woonplaats'],
            'geboortedatum'  => $validated['geboortedatum'],
            'lid_sinds'      => $validated['lid_sinds'],
            'lid_type'       => $validated['lid_type'],
        ]);

        if (auth()->check()) {
            Activiteit::log(auth()->id(), 'lid_bijgewerkt', [
                'lid_id'   => $id,
                'lid_naam' => $validated['naam'],
                'details'  => 'Gebruiker ' . auth()->user()->naam . ' heeft de gegevens van ' . $validated['naam'] . ' bijgewerkt.'
            ]);
        }

        return redirect()->route('ledenpagina.show', $lid->lid_id)->with('success', 'Lid bewerkt');
    }

    // Lid verwijderen
    public function destroy(string $lidId)
    {
        Gate::authorize('leden-verwijderen');

        $lid = Lid::with('gebruiker')->where('lid_id', $lidId)->first();
        if ($lid) {
            // Naam onthouden voor de log voordat we deleten
            $naam = $lid->gebruiker ? $lid->gebruiker->naam : 'Onbekend';

            // Alles in 1 transactie: eerst gekoppelde records weg,
            // anders blokkeert de database het verwijderen (foreign keys)
            DB::transaction(function () use ($lid) {
                $betalingIds = \App\Models\Betaling::withTrashed()
                    ->where('lid_id', $lid->lid_id)
                    ->pluck('betaling_id');

                \App\Models\Bonnen::whereIn('betaling_id', $betalingIds)->delete();
                \App\Models\Betaling::withTrashed()->where('lid_id', $lid->lid_id)->forceDelete();
                \App\Models\Notificatie::where('lid_id', $lid->lid_id)->delete();

                $lid->delete();

                // Gekoppeld account deactiveren zodat er niet meer ingelogd kan worden
                if ($lid->gebruiker) {
                    $lid->gebruiker->status = 'Inactief';
                    $lid->gebruiker->save();
                }
            });

            if (auth()->check()) {
                Activiteit::log(auth()->id(), 'lid_verwijderd', [
                    'lid_id'   => $lidId,
                    'lid_naam' => $naam,
                    'details'  => 'Gebruiker ' . auth()->user()->naam . ' heeft lid ' . $naam . ' verwijderd.'
                ]);
            }
        }

        return redirect()->route('ledenpagina')->with('success', 'Lid verwijderd');
    }
}
