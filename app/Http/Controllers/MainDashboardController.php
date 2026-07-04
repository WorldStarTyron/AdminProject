<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Betaling;
use App\Models\Lid;
use Carbon\Carbon;

// Hoofd dashboard
class MainDashboardController extends Controller
{
    public function index()
    {
        return view('MainDashboardPagina');
    }

    public function Maindashboard()
    {
        // Huidige maand en jaar
        $currentMonth = now()->month;
        $currentYear = now()->year;

        // Alle kaart statistieken op 1 plek
        $stats = Betaling::dashboardStatistieken($currentMonth, $currentYear);
        $totaleInkomsten   = $stats['totaleInkomsten'];
        $openstaandBedrag  = $stats['openstaandBedrag'];
        $totaalLeden       = $stats['totaalLeden'];
        $totaalBetaald     = $stats['totaalBetaald'];
        $totaalNietBetaald = $stats['totaalNietBetaald'];

        // Aankomende deadlines
        $deadlineLeden = $this->aankomendBetalingen();

        // Leden tabel met betalingen van deze maand
        $dashboardLeden = Lid::metActieveGebruiker()
            ->with([
                'gebruiker',
                'betalingen' => function ($q) use ($currentMonth, $currentYear) {
                    $q->where('maand', $currentMonth)->where('jaar', $currentYear);
                },
            ])
            ->orderBy('lid_id', 'desc')
            ->paginate(5);

        return view('MainDashboardPagina', compact(
            'totaleInkomsten',
            'openstaandBedrag',
            'totaalLeden',
            'totaalBetaald',
            'totaalNietBetaald',
            'deadlineLeden',
            'dashboardLeden',
            'currentMonth',
            'currentYear'
        ));
    }

    // Data voor de grafiek
    public function ChartData(Request $request)
    {
        $jaar = $request->input('jaar', now()->year);
        // Lege maand = alle 12 maanden
        $selectedMaand = $request->input('maand');

        // Contributie per maand (alleen betaalde bedragen, net als op de betalingenpagina)
        $contributie = Betaling::where('jaar', $jaar)
            ->whereIn('status', ['betaald', 'goed_gekeurd'])
            ->selectRaw('maand, SUM(bedrag) as totaal')
            ->groupBy('maand')
            ->pluck('totaal', 'maand');

        // Aantal betaalde betalingen per maand
        $betaald = Betaling::where('jaar', $jaar)
            ->whereIn('status', ['betaald', 'goed_gekeurd'])
            ->selectRaw('maand, COUNT(*) as totaal')
            ->groupBy('maand')
            ->pluck('totaal', 'maand');

        // Aantal openstaande per maand
        $nietBetaald = Betaling::where('jaar', $jaar)
            ->whereIn('status', ['niet_betaald', 'Openstaand', 'in_afwachting'])
            ->selectRaw('maand, COUNT(*) as totaal')
            ->groupBy('maand')
            ->pluck('totaal', 'maand');

        if ($selectedMaand) {
            $maanden = [(int)$selectedMaand];
        } else {
            $maanden = range(1, 12);
        }

        // Jaren dropdown
        $Totaaljaren = Betaling::selectRaw('jaar')
            ->distinct()
            ->orderBy('jaar', 'desc')
            ->pluck('jaar')
            ->toArray();

        // Maanden dropdown
        $Totaalmaanden = Betaling::where('jaar', $jaar)
            ->selectRaw('maand')
            ->distinct()
            ->orderBy('maand', 'asc')
            ->pluck('maand')
            ->toArray();

        // Maandnamen voor de labels
        $maandNamen = [
            1 => 'Jan', 2 => 'Feb', 3 => 'Mrt', 4 => 'Apr',
            5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug',
            9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Dec'
        ];

        return response()->json([
            'contributie'   => array_map(fn($m) => (float)($contributie[$m] ?? 0), $maanden),
            'betaald'       => array_map(fn($m) => (int)($betaald[$m] ?? 0), $maanden),
            'niet_betaald'  => array_map(fn($m) => (int)($nietBetaald[$m] ?? 0), $maanden),
            'labels'        => array_map(fn($m) => $maandNamen[$m], $maanden),
            'Totaaljaren'   => $Totaaljaren,
            'Totaalmaanden' => $Totaalmaanden,
            'huidigJaar'    => (int)$jaar,
        ]);
    }

    // Aankomende deadlines (binnen 7 dagen of verlopen)
    public function aankomendBetalingen()
    {
        $vandaag = Carbon::today();
        $overZevenDagen = $vandaag->copy()->addDays(7);

        $alleLeden = Lid::metActieveGebruiker()
            ->with(['gebruiker', 'laatsteBetaaldeBetaling'])
            ->get();

        $deadlineLeden = $alleLeden->map(function ($lid) {
                // Deadline alvast berekenen zodat de blade hem direct kan gebruiken
                $lid->_deadline = $lid->actieveDeadline();
                return $lid;
            })
            ->filter(function ($lid) use ($vandaag, $overZevenDagen) {
                if (!$lid->_deadline) return false;
                return $lid->_deadline->lte($overZevenDagen);
            })
            ->sortBy(function ($lid) {
                // Verlopen eerst, dan op datum
                return $lid->_deadline->timestamp;
            })
            ->values();

        // Paginate de collectie
        $perPage = 5;
        $deadlinePage = request()->input('deadline_page', 1);

        return new \Illuminate\Pagination\LengthAwarePaginator(
            $deadlineLeden->forPage($deadlinePage, $perPage),
            $deadlineLeden->count(),
            $perPage,
            $deadlinePage,
            ['pageName' => 'deadline_page', 'path' => request()->url()]
        );
    }
}
