<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Gebruiker;
use App\Models\Rol; 

class RolBeheerController extends Controller
{
    /**
     * Display the role management page with list of users and their roles.
     */
    public function index(Request $request)
    {
        $query = Gebruiker::with('rollen');

        // Search filter (naam or email)
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('naam', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Role filter: show users that have a specific role
        if ($roleId = $request->get('rol_id')) {
            $query->whereHas('rollen', fn ($q) => $q->where('rollen.rol_id', $roleId));
        }

        $gebruikers = $query->orderBy('naam')->paginate(10);
        $alleRollen = Rol::orderBy('naam')->get();

        return view('RollenBeheerPagina', compact('gebruikers', 'alleRollen'));
    }

    /**
     * AJAX: search users by name/email (autocomplete).
     */
    public function searchUsers(Request $request)
    {
        $term = $request->get('q', '');

        if (strlen($term) < 2) {
            return response()->json([]);
        }

        $users = Gebruiker::where('naam', 'like', "%{$term}%")
                         ->orWhere('email', 'like', "%{$term}%")
                         ->limit(10)
                         ->get(['gebruiker_id', 'naam', 'email']);

        return response()->json($users);
    }

    /**
     * Quick role assignment: attach a role to a user.
     */
    public function assignRole(Request $request)
    {
        $validated = $request->validate([
            'gebruiker_id'         => 'required|exists:gebruikers,gebruiker_id',
            'rol_id'               => 'required|exists:rollen,rol_id',
            'tijdelijk_wachtwoord' => 'nullable|string|min:6',
            'verplicht_wijzigen'   => 'sometimes|boolean',
        ]);

        $gebruiker = Gebruiker::findOrFail($validated['gebruiker_id']);

        // Attach role if not already assigned
        if (!$gebruiker->rollen->contains('rol_id', $validated['rol_id'])) {
            $gebruiker->rollen()->attach($validated['rol_id']);
        }

        // Handle temporary password
        if (!empty($validated['tijdelijk_wachtwoord'])) {
            $gebruiker->wachtwoord_hash = bcrypt($validated['tijdelijk_wachtwoord']);
            $gebruiker->force_password_change = $validated['verplicht_wijzigen'] ?? false;
            $gebruiker->save();
        }

        return redirect()->route('rollen-beheer')
                         ->with('success', 'Rol succesvol toegewezen aan ' . $gebruiker->naam);
    }

    /**
     * GET  JSON: return the role IDs for a user (used by the edit modal).
     * PUT  form: sync all roles for a user (used by the edit modal submit).
     */
    public function getUserRoles($userId)
    {
        $gebruiker = Gebruiker::with('rollen')->findOrFail($userId);

        return response()->json([
            'role_ids' => $gebruiker->rollen->pluck('rol_id')->values(),
        ]);
    }

    public function updateUserRoles(Request $request, $userId)
    {
        $validated = $request->validate([
            'rollen'   => 'array',
            'rollen.*' => 'exists:rollen,rol_id',
        ]);

        $gebruiker = Gebruiker::findOrFail($userId);
        $gebruiker->rollen()->sync($validated['rollen'] ?? []);

        return redirect()->route('rollen-beheer')
                         ->with('success', 'Rollen voor ' . $gebruiker->naam . ' zijn bijgewerkt.');
    }
}
