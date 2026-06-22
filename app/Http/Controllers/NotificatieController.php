<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notificatie;
use Illuminate\Support\Facades\Auth;

// Voor de notificatie bel in de header
class NotificatieController extends Controller
{
    public function index()
    {
        // Alleen admins zien meldingen
        if (!Auth::user()->hasAnyRole(['Administratie Medewerker', 'Applicatie Beheerder'])) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $notificatie = Notificatie::where('gebruiker_id', Auth::id())
            ->orderBy('gestuurd_op', 'desc')
            ->take(10)
            ->get();

        $ongelezen = Notificatie::where('gebruiker_id', Auth::id())
            ->where('gelezen', false)
            ->count();

        return response()->json([
            'notificaties' => $notificatie,
            'ongelezen' => $ongelezen
        ]);
    }

    // Zet alle meldingen op gelezen
    public function markeerGelezen()
    {
        if (!Auth::user()->hasAnyRole(['Administratie Medewerker', 'Applicatie Beheerder'])) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        Notificatie::where('gebruiker_id', Auth::id())
            ->where('gelezen', false)
            ->update(['gelezen' => true]);

        return response()->json(['success' => true]);
    }
}
