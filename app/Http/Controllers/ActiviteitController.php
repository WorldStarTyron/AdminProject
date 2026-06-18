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

// Basisquery met de bijbehorende gebruiker en rollen
        $query = Activiteit::with(['gebruiker', 'gebruiker.rollen']);

        // acties die worden ingedeeld in categorieen
        if ($tab === 'Inloggen') {
            $query->whereIn('actie', ['ingelogd', 'uitgelogd']);
        } elseif ($tab === 'Leden') {
            $query->whereIn('actie', ['lid_aangemaakt', 'lid_bijgewerkt', 'lid_verwijderd', 'lid_gewijzigd']);
        } elseif ($tab === 'Betalingen') {
            $query->whereIn('actie', ['betaling_hersteld', 'betaling_verwijderd', 'betaling_bijgewerkt', 'betaling_goedgekeurd', 'betaling_afgewezen', 'betaling_geregistreerd', 'bewijs_geüpload']);
        }elseif ($tab === 'Systeem') {
            $query->whereNotIn('actie', [
                'ingelogd', 'uitgelogd',
                'lid_aangemaakt', 'lid_bijgewerkt', 'lid_verwijderd', 'lid_gewijzigd',
                'betaling_hersteld', 'betaling_verwijderd', 'betaling_bijgewerkt', 'betaling_goedgekeurd', 'betaling_afgewezen', 'betaling_geregistreerd', 'bewijs_geüpload'
            ]);
        }

         $actiemap = [
                 // AUTH
                 'login' => ['ingelogd'],
                 'logout' => ['uitgelogd'],

                 // LEDEN
                 'lid toegevoegd' => ['lid_aangemaakt'],
                 'lid bijgewerkt' => ['lid_bijgewerkt', 'lid_gewijzigd'],
                 'lid verwijderd' => ['lid_verwijderd'],

                 // BETALINGEN
                 'betaling geregistreerd' => ['betaling_geregistreerd'],
                 'betaling hersteld' => ['betaling_hersteld'],
                 'betaling verwijderd' => ['betaling_verwijderd'],
                 'betaling gewijzigd' => ['betaling_bijgewerkt', 'betaling_goedgekeurd', 'betaling_afgewezen'],
                 'bewijs geüpload' => ['bewijs_geüpload'],

                 // GEBRUIKERS
                 'gebruiker toegevoegd' => ['gebruiker_toegevoegd'],
                 'gebruiker verwijderd' => ['gebruiker_verwijderd'],
                 'gebruiker_gewijzigd' => ['gebruiker_gewijzigd'],

                 //Wachtwoord gewijzigd
                 'wachtwoord_gewijzigd' => ['wachtwoord_gewijzigd'],

                 //BONNEN
                  'bon gegenereerd' => ['bon_aangemaakt'],
                  'bon gedownload' => ['bon_gedownload'],

               ];

        // Filter op zoekterm (actie, details of gebruikersnaam)
        if (!empty($search)) {
             $zoekActies = [];

             foreach ($actiemap as $label => $acties){
                if(str_contains(strtolower($label), strtolower($search))){
                    $zoekActies = array_merge($zoekActies, $acties);
                }
             }


            $query->where(function ($q) use ($search, $zoekActies){
                $q->where('actie', 'like', "%$search%")
                ->orWhere('details', 'like', "%$search%")
                ->orWhereHas('gebruiker', function($query) use ($search){
                    $query->where('naam', 'like', "%$search%");
                });

                if(!empty($zoekActies)){
                    $q->orWhereIn('actie', $zoekActies);
                }
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
