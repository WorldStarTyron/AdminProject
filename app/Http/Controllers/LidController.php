<?php

namespace App\Http\Controllers;

use App\Models\Lid;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class LidController extends Controller
{
    // Leden overzichtspagina
    public function index(Request $request)
    {
        Gate::authorize('leden-bekijken');

        // Haal leden op met gebruikersgegevens
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

        // Filter op woonplaats als opgegeven
        if ($request->filled('woonplaats')) {
            $query->where('leden.woonplaats', $request->woonplaats);
        }

        // Pagineer resultaten (6 per pagina)
        $leden = $query->paginate(6)->appends($request->query());

        // Totaal aantal leden voor de stats card
        $totaalLeden = Lid::count();

        // Unieke woonplaatsen voor de filterdropdown
        $woonplaatsen = Lid::whereNotNull('woonplaats')
            ->where('woonplaats', '!=', '')
            ->distinct()
            ->orderBy('woonplaats')
            ->pluck('woonplaats');

        // Grafiekdata: aantal leden per maand voor het huidige jaar
        $chartData = Lid::selectRaw('MONTH(aangemaakt_op) as month, COUNT(*) as count')
            ->whereYear('aangemaakt_op', date('Y'))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Initialiseer alle maanden met 0
        $labels = [];
        $values = [];
        for ($m = 1; $m <= 12; $m++) {
            $labels[] = date('M', mktime(0, 0, 0, $m, 1));
            $dataPoint = $chartData->firstWhere('month', $m);
            $values[]  = $dataPoint ? $dataPoint->count : 0;
        }

        return view('ledenpagina', compact('leden', 'totaalLeden', 'labels', 'values', 'woonplaatsen'));
    }

    // Lid detailpagina
    public function show(Request $request, $id = null)
    {
        $lid = \App\Models\Lid::where('gebruiker_id', Auth::id())->firstOrFail();

        // Betalingsgeschiedenis van dit lid
        $betalingen = \App\Models\Betaling::where('lid_id', $lid->lid_id)
            ->with('bon')
            ->orderBy('ingediend_op', 'desc')
            ->paginate(5);

        // Openstaande balans (niet_betaald + Openstaand)
        $openstaandeBalans = \App\Models\Betaling::where('lid_id', $lid->lid_id)
            ->whereIn('status', ['niet_betaald', 'Openstaand'])
            ->sum('bedrag');

        // Laatste betaalde betaling
        $laatsteBetaling = \App\Models\Betaling::where('lid_id', $lid->lid_id)
            ->where('status', 'betaald')
            ->orderBy('ingediend_op', 'desc')
            ->first();

        // Zoek alleen een OPENSTAANDE betaling (niet niet_betaald)
        $UpcomingBetaling = \App\Models\Betaling::where('lid_id', $lid->lid_id)
            ->where('status', 'Openstaand')
            ->orderBy('jaar', 'desc')
            ->orderBy('maand', 'desc')
            ->first();

        if ($UpcomingBetaling) {
            // Openstaande betaling gevonden → deadline is einde van die maand
            $deadline = \Carbon\Carbon::createFromDate(
                $UpcomingBetaling->jaar,
                $UpcomingBetaling->maand, 1
            )->endOfMonth();

            $UpcomingKost = $UpcomingBetaling->bedrag;
        } else {
            // Geen openstaande betaling → pak de laatste betaalde betaling
            $UpcomingBetaling = \App\Models\Betaling::where('lid_id', $lid->lid_id)
                ->where('status', 'betaald')
                ->orderBy('jaar', 'desc')
                ->orderBy('maand', 'desc')
                ->first();

            // Deadline is einde van de VOLGENDE maand na de laatste betaling
            $deadline = $UpcomingBetaling
                ? \Carbon\Carbon::createFromDate(
                    $UpcomingBetaling->jaar,
                    $UpcomingBetaling->maand, 1
                  )->addMonth()->endOfMonth()
                : null;

            $UpcomingKost = $UpcomingBetaling ? $UpcomingBetaling->bedrag : $lid->MaandelijkseBijdrage();
        }

        return view('lidpagina', compact(
            'lid',
            'betalingen',
            'openstaandeBalans',
            'laatsteBetaling',
            'UpcomingBetaling',
            'deadline',
            'UpcomingKost'
        ));
    }

    // Lid heractiveren
    public function heractiveer($lid_id)
    {
        $lid = Lid::findOrFail($lid_id);
        $lid->gebruiker->status = 'Actief';
        $lid->gebruiker->save();

        // Activiteit loggen
        if (auth()->check()) {
            \App\Models\Activiteit::log(auth()->id(), 'lid_gewijzigd', [
                'lid_id'  => $lid->lid_id,
                'details' => 'Lid ' . $lid->gebruiker->naam . ' is succesvol hergeactiveerd.',
            ]);
        }

        return redirect()->back()->with('success', 'Account is succesvol geheractiveerd.');
    }

    // Lid deactiveren
    public function deactiveer($lid_id)
    {
        $lid = Lid::findOrFail($lid_id);
        $lid->gebruiker->status = 'Inactief';
        $lid->gebruiker->save();

        return redirect()->back()->with('success', 'Account is succesvol gedeactiveerd.');
    }
}