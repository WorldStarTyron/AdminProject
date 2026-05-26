<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Betaling;

class ChartController extends Controller
{
    public function index()
    {

        
            //aantal leden per maand toevoegen
            $ChartData = DB::table('leden')
            ->selectRaw('MONTH(lid_sinds) as maand, COUNT(*) as totaal')
            ->groupBy('maand')
            ->orderBy('maand')
            ->get();


            //Labels
            $labels = $ChartData->pluck('maand')->map(fn($m) => date('F', mktime(0,0,0,$m,1)));
        $values = $ChartData->pluck('totaal');

        return view('charts.index', compact('labels', 'values'));
        }


/**
 *Geeft de data voor betalingen over de laatste 7 maanden, uitgesplitst per methode.
 *
 * GET /betalingen/chart-data
 */
 public function chartData()
    {
        // Bouw een lijst van de laatste 7 maanden (oudste eerst)
        $maanden = collect(range(6, 0))->map(function ($maandenTerug) {
            $datum = now()->subMonths($maandenTerug);
            return [
                'jaar'  => (int) $datum->format('Y'),
                'maand' => (int) $datum->format('n'),
                'label' => $datum->translatedFormat('M'),
            ];
        });
 
        // Haal alle relevante betalingen op in één query
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
 
        // Zet om naar series die ApexCharts verwacht
        $labels     = [];
        $fysiek     = [];
        $overmaking = [];
 
        foreach ($maanden as $m) {
            $sleutel      = $m['jaar'] . '-' . $m['maand'];
            $labels[]     = $m['label'];
            $fysiek[]     = (int) ($rijen[$sleutel]['fysiek']->aantal     ?? 0);
            $overmaking[] = (int) ($rijen[$sleutel]['overmaking']->aantal ?? 0);
        }
 
        // Deze data stuur je naar je Blade component
        return response()->json([
            'labels' => $labels,
            'series' => [
                ['name' => 'Fysiek',     'data' => $fysiek],
                ['name' => 'Overmaking', 'data' => $overmaking],
            ],
        ]);
    }

    }

