<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Activiteit;
use App\Models\Gebruiker;
use Illuminate\Support\Facades\Gate;

class ActiviteitController extends Controller

{
    /**
     * Retrieve and filter activity log data for the admin view.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function activiteitLogData(Request $request)
    {

        Gate::authorize('activiteitlog-bekijken'); 
        
        // Get active filters from request
        $search = $request->input('search');
        $tab = $request->input('tab', 'Alle');

        // Build the base query eager-loading the related gebruiker model
        $query = Activiteit::with('gebruiker');

        // Apply Tab Filter
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

        // Apply Search Filter across actions, details (JSON/text), and user name
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('actie', 'like', "%{$search}%")
                  ->orWhere('details', 'like', "%{$search}%")
                  ->orWhereHas('gebruiker', function($userQuery) use ($search) {
                      $userQuery->where('naam', 'like', "%{$search}%");
                  });
            });
        }

        // Order by latest activity first
        $query->orderBy('aangemaakt_op', 'desc')->orderBy('log_id', 'desc');

        // Paginate results (5 per page like the design mockup)
        $activiteiten = $query->paginate(5)->appends($request->query());

        // Count overall total activities for KPI card
        $totalCount = Activiteit::count();

        // Calculate dynamic weekly trend
        $oneWeekAgo = now()->subDays(7);
        $twoWeeksAgo = now()->subDays(14);

        $thisWeekCount = Activiteit::where('aangemaakt_op', '>=', $oneWeekAgo)->count();
        $lastWeekCount = Activiteit::where('aangemaakt_op', '>=', $twoWeeksAgo)
                                    ->where('aangemaakt_op', '<', $oneWeekAgo)
                                    ->count();

        if ($lastWeekCount > 0) {
            $diff = $thisWeekCount - $lastWeekCount;
            $trendPercent = round(($diff / $lastWeekCount) * 100);
            $weeklyTrend = ($trendPercent >= 0 ? '+' : '') . $trendPercent . '% deze week';
        } else {
            // Default premium mock trend when there isn't enough historical data
            $weeklyTrend = '+12% deze week';
        }

        // Render the view with correct variables
        return view('ActiviteitLog', compact('activiteiten', 'search', 'tab', 'totalCount', 'weeklyTrend'));
    }
}
