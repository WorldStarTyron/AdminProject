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
    // ophalen data voor widget
    $totaleInkomsten = Betaling::where('status', 'betaald')->sum('bedrag');
    $totaalLeden = Lid::count();
    $totaalBetaald = Lid::whereHas('betalingen', function($q) {
        $q->where('status', 'betaald');
    })->count();
    $totaalNietBetaald = Lid::whereHas('betalingen', function($q) {
        $q->where('status', 'niet_betaald');
    })->count();

    // Deadline leden ophalen, die over 1 maand vervallen
    $deadlineLeden = Lid::whereHas('betalingen', function($q) {
        $q->where('status', 'niet_betaald')
          ->whereRaw('DATE_ADD(ingediend_op, INTERVAL 1 MONTH) BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 7 DAY)');
    })->with(['betalingen' => function($q) {
        $q->where('status', 'niet_betaald');
    }, 'gebruiker'])->get();

    // Leden ophalen voor het Leden Overzicht tabel op het dashboard
    $dashboardLeden = Lid::with(['gebruiker', 'betalingen'])
        ->orderBy('lid_id', 'desc')
        ->paginate(5);

    return view('MainDashboardPagina', compact(
        'totaleInkomsten',
        'totaalLeden',
        'totaalBetaald',
        'totaalNietBetaald',
        'deadlineLeden',
        'dashboardLeden'
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

    // Betaald per maand
    $betaald = Betaling::whereYear('ingediend_op', $jaar)
        ->where('status', 'betaald')
        ->selectRaw('MONTH(ingediend_op) as maand, COUNT(*) as totaal')
        ->groupByRaw('MONTH(ingediend_op)')
        ->pluck('totaal', 'maand');

    // Niet betaald per maand
    $nietBetaald = Betaling::whereYear('ingediend_op', $jaar)
        ->where('status', 'niet_betaald')
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
