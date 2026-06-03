<?php

namespace App\Http\Controllers;
use App\Models\Lid;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class LidController extends Controller
{
   


    public function index(Request $request)
    {

        Gate::authorize('leden-bekijken');

        // Select data from leden table
        // FIX: Removed 'betaalstatus' — kolom bestaat niet meer in leden tabel
        $query = Lid::select(
            'leden.lid_id',
            'gebruikers.naam',
            'leden.telefoonnummer',
            'leden.adres',
            'leden.woonplaats',
            'gebruikers.email',
            'leden.lid_sinds',
            'leden.lid_type',
        )->join('gebruikers', 'leden.gebruiker_id', '=', 'gebruikers.gebruiker_id');

        // Filter by woonplaats if provided
        if ($request->filled('woonplaats')) {
            $query->where('leden.woonplaats', $request->woonplaats);
        }

        $leden = $query->paginate(6)->appends($request->query()); // pagination: 6 members per page

        $totaalLeden = Lid::count();

        // All unique woonplaats values (unfiltered) for the filter dropdown
        $woonplaatsen = Lid::whereNotNull('woonplaats')
            ->where('woonplaats', '!=', '')
            ->distinct()
            ->orderBy('woonplaats')
            ->pluck('woonplaats');

        // Data for chart: count members per month for the current year
        $chartData = Lid::selectRaw('MONTH(aangemaakt_op) as month, COUNT(*) as count')
            ->whereYear('aangemaakt_op', date('Y'))
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

        return view('ledenpagina', compact('leden', 'totaalLeden', 'labels', 'values', 'woonplaatsen'));

    }

    // haal lid op
    public function show(Request $request, $id = null)
    {
        $lid = \App\Models\Lid::where('gebruiker_id', Auth::id())->firstOrFail();

        // Haal echte betalingen op voor dit lid
        $betalingen = \App\Models\Betaling::where('lid_id', $lid->lid_id)
            ->with('bon')
            ->orderBy('ingediend_op', 'desc')
            ->paginate(5);

        // Bereken openstaande balans (betaald)
        $openstaandeBalans = \App\Models\Betaling::where('lid_id', $lid->lid_id)
            ->where('status', 'betaald')
            ->sum('bedrag');

        // Haal de laatste succesvolle betaling op
        $laatsteBetaling = \App\Models\Betaling::where('lid_id', $lid->lid_id)
            ->where('status', 'betaald')
            ->orderBy('ingediend_op', 'desc')
            ->first();

        //Subcription betaling berekenen wanneer het lid weer moet betalen per maand.
        $UpcomingBetaling = \App\Models\Betaling::where('lid_id', $lid->lid_id)
        ->where('status', 'niet_betaald')
        ->orderBy('ingediend_op', 'asc')
        ->first();
  
        //  Subcription Betaling berekenen wanneer het lid weer moet betalen per maand
         $UpcomingBetaling = \App\Models\Betaling::where('lid_id', $lid->lid_id)
        ->where('status', 'betaald')
        ->orderBy('ingediend_op', 'asc')
        ->first();

        //wanneer lid lid wordt dan moet hij over 1 maand zijn contributie betalen.
       $deadline = $UpcomingBetaling
    ? \Carbon\Carbon::parse($UpcomingBetaling->ingediend_op)->addMonth()
    : null;
        // $Datum = Subscriptie::where('lid_id', $lid->lid_id)->first();
        

        return view('lidpagina', compact('lid', 'betalingen', 'openstaandeBalans', 'laatsteBetaling', 'UpcomingBetaling', 'deadline'));
    }

}

 