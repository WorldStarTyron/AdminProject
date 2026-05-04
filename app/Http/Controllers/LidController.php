<?php

namespace App\Http\Controllers;
use App\Models\Lid;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LidController extends Controller
{
    public function index()
    {
        $leden = Lid::select(
            'leden.lid_id',
            'gebruikers.naam',
            'leden.telefoonnummer',
            'leden.adres',
            'gebruikers.email'
        )->join('gebruikers','leden.gebruiker_id','=','gebruikers.gebruiker_id')
        ->paginate(6); // paginatie op 6 leden per pagina
      

        $totaalLeden = Lid::count();

        // Data for chart: count joins per month for the current year
        $chartData = Lid::selectRaw('MONTH(lid_sinds) as month, COUNT(*) as count')
            ->whereYear('lid_sinds', date('Y'))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $labels = [];
        $values = [];
        
        // Initialize all months with 0
        for ($m = 1; $m <= 12; $m++) {
            $monthName = date('M', mktime(0, 0, 0, $m, 1));
            $labels[] = $monthName;
            $dataPoint = $chartData->firstWhere('month', $m);
            $values[] = $dataPoint ? $dataPoint->count : 0;
        }

        return view('ledenpagina', compact('leden', 'totaalLeden', 'labels', 'values'));
    }
}
