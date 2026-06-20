<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Betaling;
use App\Models\Lid;
use Carbon\Carbon;

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

    // Haal aankomende betalingen op via aparte methode
    $deadlineLeden = $this->aankomendBetalingen();

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



    // Contributie: som van bedrag per maand waarvoor betaald moet worden
    // Groepeer op 'maand' veld uit de betalingen tabel
    $contributie = Betaling::where('jaar', $jaar)
        ->selectRaw('maand, SUM(bedrag) as totaal')
        ->groupBy('maand')
        ->pluck('totaal', 'maand');

    // Betaald: aantal betaalde betalingen per maand
    // Status 'betaald' of 'goed_gekeurd' telt als betaald
    $betaald = Betaling::where('jaar', $jaar)
        ->whereIn('status', ['betaald', 'goed_gekeurd'])
        ->selectRaw('maand, COUNT(*) as totaal')
        ->groupBy('maand')
        ->pluck('totaal', 'maand');

    // Niet betaald: aantal openstaande betalingen per maand
    // Status 'niet_betaald', 'Openstaand' of 'in_afwachting' telt als niet betaald
    $nietBetaald = Betaling::where('jaar', $jaar)
        ->whereIn('status', ['niet_betaald', 'Openstaand', 'in_afwachting'])
        ->selectRaw('maand, COUNT(*) as totaal')
        ->groupBy('maand')
        ->pluck('totaal', 'maand');

    // Bepaal welke maanden we tonen (filter op 1 maand of alle 12)
    if ($selectedMaand) {
        $maanden = [(int)$selectedMaand];
    } else {
        $maanden = range(1, 12);
    }

    // Alle beschikbare jaren voor de dropdown (uit de betalingen tabel)
    $Totaaljaren = Betaling::selectRaw('jaar')
        ->distinct()
        ->orderBy('jaar', 'desc')
        ->pluck('jaar')
        ->toArray();

    // Alle beschikbare maanden voor het geselecteerde jaar
    $Totaalmaanden = Betaling::where('jaar', $jaar)
        ->selectRaw('maand')
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
        // Zet alle data om naar de juiste volgorde per maand
        'contributie'   => array_map(fn($m) => (float)($contributie[$m] ?? 0), $maanden),
        'betaald'       => array_map(fn($m) => (int)($betaald[$m] ?? 0), $maanden),
        'niet_betaald'  => array_map(fn($m) => (int)($nietBetaald[$m] ?? 0), $maanden),
        'labels'        => array_map(fn($m) => $maandNamen[$m], $maanden),
        'Totaaljaren'   => $Totaaljaren,
        'Totaalmaanden' => $Totaalmaanden,
        'huidigJaar'    => (int)$jaar,
    ]);
}

    public function aankomendBetalingen()
    {
        $vandaag = Carbon::today();
        $overZevenDagen = $vandaag->copy()->addDays(7);

        // Haal alle actieve leden op met hun meest recente betaalde betaling
        $alleLeden = Lid::metActieveGebruiker()
            ->with(['gebruiker', 'laatsteBetaaldeBetaling'])
            ->get();

        // Filter en sorteer leden op basis van hun actieve deadline
        $deadlineLeden = $alleLeden->map(function ($lid) {
                $lid->_deadline = $lid->actieveDeadline();
                return $lid;
            })
            ->filter(function ($lid) use ($vandaag, $overZevenDagen) {
                // Toon alleen leden met een deadline die verlopen is of binnen 7 dagen valt
                if (!$lid->_deadline) return false;
                return $lid->_deadline->lte($overZevenDagen);
            })
            ->sortBy(function ($lid) {
                // Verlopen deadlines eerst, dan op datum
                return $lid->_deadline->timestamp;
            })
            ->values();

        // Pagineer de collectie
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
