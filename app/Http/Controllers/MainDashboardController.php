<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Betaling;
use App\Models\Lid;

class MainDashboardController extends Controller
{
    public function index(){
        return view('MainDashboardPagina');
    }


public function Maindashboard()
{
    // Huidige maand en jaar voor alle dashboard berekeningen
    $currentMonth = now()->month;
    $currentYear = now()->year;

    // Alle kaart-statistieken op één plek berekenen (Betaling model)
    $stats = Betaling::dashboardStatistieken($currentMonth, $currentYear);
    $totaleInkomsten   = $stats['totaleInkomsten'];
    $openstaandBedrag  = $stats['openstaandBedrag'];
    $totaalLeden       = $stats['totaalLeden'];
    $totaalBetaald     = $stats['totaalBetaald'];
    $totaalNietBetaald = $stats['totaalNietBetaald'];

// Deadline leden: openstaande betalingen waarvan de maand-deadline binnen 7 dagen valt
    // Sorted by jaar DESC, maand DESC to get the LATEST payment first for deadline display
    $deadlineLeden = Lid::metActieveGebruiker()
        ->whereHas('betalingen', function ($q) {
            $q->whereIn('status', Betaling::ONBETAALDE_STATUSSEN)
              ->whereRaw(
                  'LAST_DAY(STR_TO_DATE(CONCAT(jaar, "-", maand, "-01"), "%Y-%m-%d")) BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)'
              );
        })
        ->with(['betalingen' => function ($q) {
            // Sort by jaar DESC, maand DESC so the first() gets the LATEST openstaande betaling
            // This ensures the deadline shown is from the most recent payment
            $q->whereIn('status', Betaling::ONBETAALDE_STATUSSEN)
              ->orderBy('jaar', 'desc')
              ->orderBy('maand', 'desc');
        }, 'gebruiker'])
        ->get();

    // Leden voor het dashboard-tabel: alleen actieve leden, met betalingen van de huidige maand
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



public function ChartData(Request $request)
{
    // Gebruik het geselecteerde jaar, of het huidige jaar als default
    $jaar = $request->input('jaar', now()->year);
    $selectedMaand = $request->input('maand'); // null = alle maanden

    // Contributie (som van bedrag) per maand
    $contributie = Betaling::whereYear('ingediend_op', $jaar)
        ->selectRaw('MONTH(ingediend_op) as maand, SUM(bedrag) as totaal')
        ->groupByRaw('MONTH(ingediend_op)')
        ->pluck('totaal', 'maand');

    // Totaal aantal leden per maand (op basis van aanmaakdatum lid)
    $leden = Lid::whereYear('lid_sinds', $jaar)
        ->selectRaw('MONTH(lid_sinds) as maand, COUNT(*) as totaal')
        ->groupByRaw('MONTH(lid_sinds)')
        ->pluck('totaal', 'maand');

// Betaald per maand (includes both 'betaald' and 'goed_gekeurd')
    $betaald = Betaling::whereYear('ingediend_op', $jaar)
        ->whereIn('status', ['betaald', 'goed_gekeurd'])
        ->selectRaw('MONTH(ingediend_op) as maand, COUNT(*) as totaal')
        ->groupByRaw('MONTH(ingediend_op)')
        ->pluck('totaal', 'maand');

    // Niet betaald per maand (includes all unpaid statuses)
    $nietBetaald = Betaling::whereYear('ingediend_op', $jaar)
        ->whereIn('status', ['niet_betaald', 'Openstaand', 'in_afwachting'])
        ->selectRaw('MONTH(ingediend_op) as maand, COUNT(*) as totaal')
        ->groupByRaw('MONTH(ingediend_op)')
        ->pluck('totaal', 'maand');

    // Bepaal welke maanden we tonen (filter op 1 maand of alle 12)
    if ($selectedMaand) {
        $maanden = [(int)$selectedMaand];
    } else {
        $maanden = range(1, 12);
    }

    // Alle beschikbare jaren voor de dropdown
    $Totaaljaren = Betaling::selectRaw('YEAR(ingediend_op) as jaar')
        ->distinct()
        ->orderBy('jaar', 'desc')
        ->pluck('jaar')
        ->toArray();

    // Alle beschikbare maanden voor het geselecteerde jaar
    $Totaalmaanden = Betaling::whereYear('ingediend_op', $jaar)
        ->selectRaw('MONTH(ingediend_op) as maand')
        ->distinct()
        ->orderBy('maand', 'asc')
        ->pluck('maand')
        ->toArray();

    // Maandnamen voor de x-as labels
    $maandNamen = [
        1 => 'Jan', 2 => 'Feb', 3 => 'Mrt', 4 => 'Apr',
        5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug',
        9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Dec'
    ];

    return response()->json([
        'contributie'   => array_map(fn($m) => (float)($contributie[$m] ?? 0), $maanden),
        'leden'         => array_map(fn($m) => (int)($leden[$m] ?? 0), $maanden),
        'betaald'       => array_map(fn($m) => (int)($betaald[$m] ?? 0), $maanden),
        'niet_betaald'  => array_map(fn($m) => (int)($nietBetaald[$m] ?? 0), $maanden),
        'labels'        => array_map(fn($m) => $maandNamen[$m], $maanden),
        'Totaaljaren'   => $Totaaljaren,
        'Totaalmaanden' => $Totaalmaanden,
        'huidigJaar'    => (int)$jaar,
    ]);
}


}
