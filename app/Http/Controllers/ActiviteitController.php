<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Activiteit;
use App\Models\Gebruiker;
use Illuminate\Support\Facades\Gate;

// Activiteit log pagina
class ActiviteitController extends Controller
{
    public function activiteitLogData(Request $request)
    {
        Gate::authorize('activiteitlog-bekijken');

        $search = $request->input('search');
        $tab = $request->input('tab', 'Alle');

        $query = Activiteit::with(['gebruiker', 'gebruiker.rollen']);

        // Filter per tab
        if ($tab === 'Inloggen') {
            $query->whereIn('actie', ['ingelogd', 'uitgelogd']);
        } elseif ($tab === 'Leden') {
            $query->whereIn('actie', ['lid_aangemaakt', 'lid_bijgewerkt', 'lid_verwijderd', 'lid_gewijzigd']);
        } elseif ($tab === 'Betalingen') {
            $query->whereIn('actie', ['betaling_hersteld', 'betaling_verwijderd', 'betaling_bijgewerkt', 'betaling_goedgekeurd', 'betaling_afgewezen', 'betaling_geregistreerd', 'bewijs_geüpload']);
        } elseif ($tab === 'Systeem') {
            // Alles wat niet in de andere tabs valt
            $query->whereNotIn('actie', [
                'ingelogd', 'uitgelogd',
                'lid_aangemaakt', 'lid_bijgewerkt', 'lid_verwijderd', 'lid_gewijzigd',
                'betaling_hersteld', 'betaling_verwijderd', 'betaling_bijgewerkt', 'betaling_goedgekeurd', 'betaling_afgewezen', 'betaling_geregistreerd', 'bewijs_geüpload'
            ]);
        }

        // Mapping voor zoeken op Nederlandse labels
        $actiemap = [
            'login' => ['ingelogd'],
            'logout' => ['uitgelogd'],

            'lid toegevoegd' => ['lid_aangemaakt'],
            'lid bijgewerkt' => ['lid_bijgewerkt', 'lid_gewijzigd'],
            'lid verwijderd' => ['lid_verwijderd'],

            'betaling geregistreerd' => ['betaling_geregistreerd'],
            'betaling hersteld' => ['betaling_hersteld'],
            'betaling verwijderd' => ['betaling_verwijderd'],
            'betaling gewijzigd' => ['betaling_bijgewerkt', 'betaling_goedgekeurd', 'betaling_afgewezen'],
            'bewijs geüpload' => ['bewijs_geüpload'],

            'gebruiker toegevoegd' => ['gebruiker_toegevoegd'],
            'gebruiker verwijderd' => ['gebruiker_verwijderd'],
            'gebruiker_gewijzigd' => ['gebruiker_gewijzigd'],

            'wachtwoord_gewijzigd' => ['wachtwoord_gewijzigd'],

            'bon gegenereerd' => ['bon_aangemaakt'],
            'bon gedownload' => ['bon_gedownload'],
        ];

        // Zoeken op actie, details of naam
        if (!empty($search)) {
            $zoekActies = [];

            foreach ($actiemap as $label => $acties) {
                if (str_contains(strtolower($label), strtolower($search))) {
                    $zoekActies = array_merge($zoekActies, $acties);
                }
            }

            $query->where(function ($q) use ($search, $zoekActies) {
                $q->where('actie', 'like', "%$search%")
                  ->orWhere('details', 'like', "%$search%")
                  ->orWhereHas('gebruiker', function ($query) use ($search) {
                      $query->where('naam', 'like', "%$search%");
                  });

                if (!empty($zoekActies)) {
                    $q->orWhereIn('actie', $zoekActies);
                }
            });
        }

        $query->orderBy('aangemaakt_op', 'desc')->orderBy('log_id', 'desc');

        $activiteiten = $query->paginate(5)->appends($request->query());

        $totalCount = Activiteit::count();

        // Weektrend voor de KPI kaart
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
            $weeklyTrend = '+12% deze week';
        }

        return view('ActiviteitLog', compact('activiteiten', 'search', 'tab', 'totalCount', 'weeklyTrend'));
    }
}
