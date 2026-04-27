<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
    }

