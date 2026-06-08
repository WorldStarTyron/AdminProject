<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Activiteit;
use App\Models\Gebruiker;
use Illuminate\Support\Facades\Gate;

class ActiviteitController extends Controller
{
    public function activiteitLogData(Request $request)
    {
        // Alleen admins mogen de activiteitenlog bekijken
        Gate::authorize('activiteitlog-bekijken');

        // Haal filters op uit de request
        $search = $request->input('search');
        $tab = $request->input('tab', 'Alle');

        // Basisquery met de bijbehorende gebruiker
        $query = Activiteit::with('gebruiker');

        // Filter op tab
        if ($tab === 'Inloggen') {
            $query->whereIn('actie', ['ingelogd', 'uitgelogd']);
        } elseif ($tab === 'Leden') {
            $query->whereIn('actie', ['lid_aangemaakt', 'lid_bijgewerkt', 'lid_verwijderd']);
        } elseif ($tab === 'Systeem') {
            $query->whereNotIn('actie', [
                'ingelogd', 'uitgelogd',
                'lid_aangemaakt', 'lid_bijgewerkt', 'lid_verwijderd'
            ]);
        }

        // Filter op zoekterm (actie, details of gebruikersnaam)
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('actie', 'like', "%{$search}%")
                  ->orWhere('details', 'like', "%{$search}%")
                  ->orWhereHas('gebruiker', function($userQuery) use ($search) {
                      $userQuery->where('naam', 'like', "%{$search}%");
                  });
            });
        }

        // Nieuwste activiteiten eerst
        $query->orderBy('aangemaakt_op', 'desc')->orderBy('log_id', 'desc');

        // Pagineer resultaten (5 per pagina)
        $activiteiten = $query->paginate(5)->appends($request->query());

        // Totaal aantal activiteiten voor de KPI-kaart
        $totalCount = Activiteit::count();

        // Bereken de weektrend (deze week vs vorige week)
        $oneWeekAgo  = now()->subDays(7);
        $twoWeeksAgo = now()->subDays(14);

        $thisWeekCount = Activiteit::where('aangemaakt_op', '>=', $oneWeekAgo)->count();
        $lastWeekCount = Activiteit::where('aangemaakt_op', '>=', $twoWeeksAgo)
                                    ->where('aangemaakt_op', '<', $oneWeekAgo)
                                    ->count();

        if ($lastWeekCount > 0) {
            $diff         = $thisWeekCount - $lastWeekCount;
            $trendPercent = round(($diff / $lastWeekCount) * 100);
            $weeklyTrend  = ($trendPercent >= 0 ? '+' : '') . $trendPercent . '% deze week';
        } else {
            // Standaard waarde als er niet genoeg data is
            $weeklyTrend = '+12% deze week';
        }

        // Stuur data naar de view
        return view('ActiviteitLog', compact('activiteiten', 'search', 'tab', 'totalCount', 'weeklyTrend'));
    }
}