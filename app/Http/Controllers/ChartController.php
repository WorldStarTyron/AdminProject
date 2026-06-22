<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Betaling;

// Grafiek data
class ChartController extends Controller
{
    public function index()
    {
        // Aantal leden per maand
        $ChartData = DB::table('leden')
            ->selectRaw('MONTH(lid_sinds) as maand, COUNT(*) as totaal')
            ->groupBy('maand')
            ->orderBy('maand')
            ->get();

        $labels = $ChartData->pluck('maand')->map(fn($m) => date('F', mktime(0,0,0,$m,1)));
        $values = $ChartData->pluck('totaal');

        return view('charts.index', compact('labels', 'values'));
    }

    // Betalingen van de laatste 7 maanden per methode
    public function chartData()
    {
        // Lijst maken van de laatste 7 maanden
        $maanden = collect(range(6, 0))->map(function ($maandenTerug) {
            $datum = now()->subMonths($maandenTerug);
            return [
                'jaar'  => (int) $datum->format('Y'),
                'maand' => (int) $datum->format('n'),
                'label' => $datum->translatedFormat('M'),
            ];
        });

        // In 1 query alles ophalen
        $rijen = Betaling::selectRaw('jaar, maand, methode, COUNT(DISTINCT lid_id) as aantal')
            ->where('status', 'betaald')
            ->where(function ($q) use ($maanden) {
                foreach ($maanden as $m) {
                    $q->orWhere(function ($sub) use ($m) {
                        $sub->where('jaar', $m['jaar'])
                            ->where('maand', $m['maand']);
                    });
                }
            })
            ->groupBy('jaar', 'maand', 'methode')
            ->get()
            ->groupBy(fn($r) => $r->jaar . '-' . $r->maand)
            ->map(fn($groep) => $groep->keyBy('methode'));

        // Omzetten naar series voor ApexCharts
        $labels     = [];
        $fysiek     = [];
        $overmaking = [];

        foreach ($maanden as $m) {
            $sleutel      = $m['jaar'] . '-' . $m['maand'];
            $labels[]     = $m['label'];
            $fysiek[]     = (int) ($rijen[$sleutel]['fysiek']->aantal     ?? 0);
            $overmaking[] = (int) ($rijen[$sleutel]['overmaking']->aantal ?? 0);
        }

        return response()->json([
            'labels' => $labels,
            'series' => [
                ['name' => 'Fysiek',     'data' => $fysiek],
                ['name' => 'Overmaking', 'data' => $overmaking],
            ],
        ]);
    }
}
