<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Gebruiker;
use App\Models\Rol;
use App\Models\Activiteit;
use Illuminate\Support\Facades\Gate;

// Rollen beheer
class RolBeheerController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('rollenbeheer');

        $query = Gebruiker::with('rollen');

        // Zoeken op naam of email
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('naam', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter op rol
        if ($roleId = $request->get('rol_id')) {
            $query->whereHas('rollen', fn ($q) => $q->where('rollen.rol_id', $roleId));
        }

        $gebruikers = $query->orderBy('naam')->paginate(10);
        $alleRollen = Rol::where('naam', '!=', 'Applicatie Beheerder')->orderBy('naam')->get();

        return view('RollenBeheerPagina', compact('gebruikers', 'alleRollen'));
    }

    // AJAX zoekfunctie voor autocomplete
    public function searchUsers(Request $request)
    {
        $term = $request->get('q', '');

        // Minimaal 2 tekens
        if (strlen($term) < 2) {
            return response()->json([]);
        }

        $users = Gebruiker::where('naam', 'like', "%{$term}%")
                         ->orWhere('email', 'like', "%{$term}%")
                         ->limit(10)
                         ->get(['gebruiker_id', 'naam', 'email']);

        return response()->json($users);
    }

    // Rol toewijzen
    public function assignRole(Request $request)
    {
        $validated = $request->validate([
            'gebruiker_id' => 'required|exists:gebruikers,gebruiker_id',
            'rol_id'       => 'required|exists:rollen,rol_id',
        ]);

        $gebruiker = Gebruiker::findOrFail($validated['gebruiker_id']);
        $rol = Rol::findOrFail($validated['rol_id']);

        // De beheerder-rol mag niet via dit formulier worden toegewezen
        if (strtolower($rol->naam) === 'applicatie beheerder') {
            return redirect()->route('rollen-beheer')
                             ->with('error', 'De rol Applicatie Beheerder kan hier niet worden toegewezen.');
        }

        // Eerst checken om dubbele rollen te voorkomen
        $alreadyHasRole = $gebruiker->rollen->contains('rol_id', $rol->rol_id);
        if (!$alreadyHasRole) {
            $gebruiker->rollen()->attach($rol->rol_id);

            if (auth()->check()) {
                Activiteit::log(auth()->id(), 'gebruiker_gewijzigd', [
                    'doel_gebruiker' => $gebruiker->naam,
                    'rol_toegevoegd' => $rol->naam,
                    'details'        => 'Rol ' . $rol->naam . ' toegewezen aan gebruiker ' . $gebruiker->naam . '.'
                ]);
            }
        }

        return redirect()->route('rollen-beheer')
                         ->with('success', 'Rol succesvol toegewezen aan ' . $gebruiker->naam);
    }

    // Rol IDs van een gebruiker ophalen
    public function getUserRoles($userId)
    {
        $gebruiker = Gebruiker::with('rollen')->findOrFail($userId);

        return response()->json([
            'role_ids' => $gebruiker->rollen->pluck('rol_id')->values(),
        ]);
    }

    // Sync alle rollen in 1 keer
    public function updateUserRoles(Request $request, $userId)
    {
        $validated = $request->validate([
            'rollen'   => 'array',
            'rollen.*' => 'exists:rollen,rol_id',
        ]);

        $gebruiker = Gebruiker::findOrFail($userId);
        // ?? [] zorgt dat lege selectie alle rollen verwijdert
        $nieuweRollen = $validated['rollen'] ?? [];

        // De beheerder-rol kan hier niet worden toegevoegd of afgenomen
        // (voorkomt ook dat de enige beheerder zichzelf buitensluit)
        $beheerderRol = Rol::whereRaw('LOWER(naam) = ?', ['applicatie beheerder'])->first();
        if ($beheerderRol) {
            $nieuweRollen = array_diff($nieuweRollen, [$beheerderRol->rol_id]);

            $heeftBeheerderRol = $gebruiker->rollen->contains('rol_id', $beheerderRol->rol_id);
            if ($heeftBeheerderRol) {
                $nieuweRollen[] = $beheerderRol->rol_id;
            }
        }

        $gebruiker->rollen()->sync($nieuweRollen);

        if (auth()->check()) {
            Activiteit::log(auth()->id(), 'gebruiker_gewijzigd', [
                'gebruiker_id'   => $userId,
                'gebruiker_naam' => $gebruiker->naam,
                'details'        => 'Rollen bijgewerkt voor gebruiker ' . $gebruiker->naam . ' door beheerder.'
            ]);
        }

        return redirect()->route('rollen-beheer')
                         ->with('success', 'Rollen voor ' . $gebruiker->naam . ' zijn bijgewerkt.');
    }
}
