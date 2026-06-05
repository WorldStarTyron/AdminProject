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

        // Bereken openstaande balans (niet betaald + openstaand)
        $openstaandeBalans = \App\Models\Betaling::where('lid_id', $lid->lid_id)
            ->whereIn('status', ['niet_betaald', 'Openstaand'])
            ->sum('bedrag');

        // Haal de laatste succesvolle betaling op
        $laatsteBetaling = \App\Models\Betaling::where('lid_id', $lid->lid_id)
            ->where('status', 'betaald')
            ->orderBy('ingediend_op', 'desc')
            ->first();

        // Bepaal de eerstvolgende betaling die gedaan moet worden
        $UpcomingBetaling = \App\Models\Betaling::where('lid_id', $lid->lid_id)
            ->whereIn('status', ['Openstaand', 'niet_betaald'])
            ->orderBy('jaar', 'asc')
            ->orderBy('maand', 'asc')
            ->first();

        if ($UpcomingBetaling) {
    // Openstaande betaling: deadline is einde van die maand
    $deadline = \Carbon\Carbon::createFromDate(
        $UpcomingBetaling->jaar, 
        $UpcomingBetaling->maand, 1
    )->endOfMonth();
} else {
    // Laatste betaalde betaling ophalen
    $UpcomingBetaling = \App\Models\Betaling::where('lid_id', $lid->lid_id)
        ->where('status', 'betaald')
        ->orderBy('jaar', 'desc')
        ->orderBy('maand', 'desc')
        ->first();

    if ($UpcomingBetaling) {
    $deadline = \Carbon\Carbon::createFromDate(
        $UpcomingBetaling->jaar, 
        $UpcomingBetaling->maand, 1
    )->endOfMonth();
} else {
    $UpcomingBetaling = \App\Models\Betaling::where('lid_id', $lid->lid_id)
        ->where('status', 'betaald')
        ->orderBy('jaar', 'desc')
        ->orderBy('maand', 'desc')
        ->first();

    // ✅ addMonth() BEFORE endOfMonth() — deadline is end of NEXT month
    $deadline = $UpcomingBetaling
        ? \Carbon\Carbon::createFromDate(
            $UpcomingBetaling->jaar, 
            $UpcomingBetaling->maand, 1
          )->addMonth()->endOfMonth()
        : null;
}
}
        

        return view('lidpagina', compact('lid', 'betalingen', 'openstaandeBalans', 'laatsteBetaling', 'UpcomingBetaling', 'deadline'));
    }

    public function heractiveer($lid_id)
    {
        $lid = Lid::findOrFail($lid_id);
        $lid->gebruiker->status = 'Actief';
        $lid->gebruiker->save();

        if (auth()->check()) {
            \App\Models\Activiteit::log(auth()->id(), 'lid_gewijzigd', [
                'lid_id'  => $lid->lid_id,
                'details' => 'Lid ' . $lid->gebruiker->naam . ' is succesvol hergeactiveerd.'
            ]);
        }

         return redirect()->back()->with('success', 'Account is succesvol geheractiveerd.');




}
    public function deactiveer($lid_id)
{
    $lid = Lid::findOrFail($lid_id);
    $lid->gebruiker->status = 'Inactief';
    $lid->gebruiker->save();

    return redirect()->back()->with('success', 'Account is succesvol gedeactiveerd.');
}
    
}

 