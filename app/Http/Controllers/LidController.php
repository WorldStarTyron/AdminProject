<?php

namespace App\Http\Controllers;

use App\Models\Lid;
use App\Models\Betaling;
use App\Models\Gebruiker;
use App\Models\Notificatie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

// Ledenoverzicht, lid profiel en upload functie
class LidController extends Controller
{
    // Leden overzichtspagina
    public function index(Request $request)
    {
        Gate::authorize('leden-bekijken');

        // Join met gebruikers want naam en email staan daar
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

        // Filter op woonplaats
        if ($request->filled('woonplaats')) {
            $query->where('leden.woonplaats', $request->woonplaats);
        }

        // Zoeken op 5 velden tegelijk
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('gebruikers.naam', 'LIKE', "%{$search}%")
                  ->orWhere('leden.telefoonnummer', 'LIKE', "%{$search}%")
                  ->orWhere('gebruikers.email', 'LIKE', "%{$search}%")
                  ->orWhere('leden.adres', 'LIKE', "%{$search}%")
                  ->orWhere('leden.woonplaats', 'LIKE', "%{$search}%");
            });
        }

        // appends() zorgt dat filter blijft bij paginering
        $leden = $query->paginate(6)->appends($request->query());

        $totaalLeden = Lid::count();

        // Woonplaatsen voor de dropdown
        $woonplaatsen = Lid::whereNotNull('woonplaats')
            ->where('woonplaats', '!=', '')
            ->distinct()
            ->orderBy('woonplaats')
            ->pluck('woonplaats');

        // Nieuwe leden per maand
        $chartData = Lid::selectRaw('MONTH(aangemaakt_op) as month, COUNT(*) as count')
            ->whereYear('aangemaakt_op', date('Y'))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Vul alle 12 maanden, ook lege
        $labels = [];
        $values = [];
        for ($m = 1; $m <= 12; $m++) {
            $labels[] = date('M', mktime(0, 0, 0, $m, 1));
            $dataPoint = $chartData->firstWhere('month', $m);
            $values[]  = $dataPoint ? $dataPoint->count : 0;
        }

        return view('ledenpagina', compact('leden', 'totaalLeden', 'labels', 'values', 'woonplaatsen'));
    }

    // Verwijder dubbele betalingen zonder methode
    public function removeduplicateBetalingen()
    {
        $huidigeMaand = now()->month;
        $huidigeJaar = now()->year;

        // Alleen betalingen zonder methode (= nog niet verwerkt)
        $betalingen = Betaling::whereNull('methode')
            ->where('maand', $huidigeMaand)
            ->where('jaar', $huidigeJaar)
            ->get();

        $verwijderd = 0;

        foreach ($betalingen as $betaling) {
            $betaling->delete();
            $verwijderd++;
        }

        return redirect()->back()->with('success', $verwijderd . ' dubbele betaling(en) verwijderd');
    }

    // Eigen lidpagina (mijn gegevens)
    public function show(Request $request, $id = null)
    {
        // $id wordt genegeerd, alleen eigen lid via Auth::id()
        $lid = \App\Models\Lid::where('gebruiker_id', Auth::id())->firstOrFail();

        // Openstaande balans
        $openstaandeBalans = \App\Models\Betaling::where('lid_id', $lid->lid_id)
            ->whereIn('status', ['niet_betaald', 'Openstaand'])
            ->sum('bedrag');

        // Laatste betaalde betaling
        $laatsteBetaling = \App\Models\Betaling::where('lid_id', $lid->lid_id)
            ->where('status', 'betaald')
            ->orderBy('ingediend_op', 'desc')
            ->first();

        // Zoek een openstaande betaling
        $UpcomingBetaling = \App\Models\Betaling::where('lid_id', $lid->lid_id)
            ->where('status', 'Openstaand')
            ->orderBy('jaar', 'desc')
            ->orderBy('maand', 'desc')
            ->first();

        if ($UpcomingBetaling) {
            // Deadline = einde maand
            $deadline = \Carbon\Carbon::createFromDate(
                $UpcomingBetaling->jaar,
                $UpcomingBetaling->maand, 1
            )->endOfMonth();

            $UpcomingKost = $UpcomingBetaling->bedrag;
        } else {
            // Geen openstaande, gebruik laatste betaalde
            $UpcomingBetaling = \App\Models\Betaling::where('lid_id', $lid->lid_id)
                ->where('status', 'betaald')
                ->orderBy('jaar', 'desc')
                ->orderBy('maand', 'desc')
                ->first();

            // Volgende deadline = einde van de volgende maand
            $deadline = $UpcomingBetaling
                ? \Carbon\Carbon::createFromDate(
                    $UpcomingBetaling->jaar,
                    $UpcomingBetaling->maand, 1
                  )->addMonth()->endOfMonth()
                : null;

            $UpcomingKost = $UpcomingBetaling
                ? $UpcomingBetaling->bedrag
                : $lid->MaandelijkseBijdrage();
        }

        // Filter op maand/jaar
        $maandFilter = $request->input('maand');
        $jaarFilter  = $request->input('jaar');

        $betalingenQuery = \App\Models\Betaling::where('lid_id', $lid->lid_id)
            ->with('bon')
            ->orderBy('ingediend_op', 'desc');

        if ($maandFilter) {
            $betalingenQuery->whereMonth('ingediend_op', $maandFilter);
        }

        if ($jaarFilter) {
            $betalingenQuery->whereYear('ingediend_op', $jaarFilter);
        }

        $betalingen = $betalingenQuery->paginate(5)->withQueryString();

        // Beschikbare jaren voor de dropdown
        $beschikbareJaren = \App\Models\Betaling::where('lid_id', $lid->lid_id)
            ->selectRaw('YEAR(ingediend_op) as jaar')
            ->groupByRaw('YEAR(ingediend_op)')
            ->orderByDesc('jaar')
            ->pluck('jaar');

        // Check of er deze maand al is betaald
        $currentMonth = now()->month;
        $currentYear = now()->year;
        $hasPaidThisMonth = \App\Models\Betaling::where('lid_id', $lid->lid_id)
            ->where('maand', $currentMonth)
            ->where('jaar', $currentYear)
            ->whereIn('status', ['betaald', 'goed_gekeurd'])
            ->exists();

        return view('lidpagina', compact(
            'lid',
            'betalingen',
            'openstaandeBalans',
            'laatsteBetaling',
            'UpcomingBetaling',
            'deadline',
            'UpcomingKost',
            'beschikbareJaren',
            'hasPaidThisMonth'
        ));
    }

    // Lid heractiveren
    public function heractiveer($lid_id)
    {
        Gate::authorize('leden-heractiveren');

        $lid = Lid::findOrFail($lid_id);
        $lid->gebruiker->status = 'Actief';
        $lid->gebruiker->save();

        if (auth()->check()) {
            \App\Models\Activiteit::log(auth()->id(), 'lid_gewijzigd', [
                'lid_id'  => $lid->lid_id,
                'details' => 'Lid ' . $lid->gebruiker->naam . ' is succesvol heractiveerd.',
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

        if (auth()->check()) {
            \App\Models\Activiteit::log(auth()->id(), 'lid_gewijzigd', [
                'lid_id'  => $lid->lid_id,
                'details' => 'Lid ' . $lid->gebruiker->naam . ' is succesvol gedeactiveerd.',
            ]);
        }

        return redirect()->back()->with('success', 'Account is succesvol gedeactiveerd.');
    }

    // Betalingsbewijs uploaden (PDF, max 5MB)
    public function UploadBewijs(Request $request)
    {
        $request->validate([
            'betaling_bewijs' => 'required|file|mimes:pdf|max:5120',
        ]);

        $lid = Lid::where('gebruiker_id', Auth::id())->firstOrFail();

        $bewijs = $request->file('betaling_bewijs');
        $pad = $bewijs->store('bewijzen', 'public');

        // Oudste openstaande maand pakken
        $betaling = Betaling::where('lid_id', $lid->lid_id)
            ->whereIn('status', ['Openstaand', 'niet_betaald'])
            ->orderBy('jaar', 'asc')
            ->orderBy('maand', 'asc')
            ->first();

        if ($betaling) {
            // Status wordt in_afwachting tot admin het beoordeelt
            $betaling->update([
                'betaling_bewijs' => $pad,
                'status' => 'in_afwachting',
                'ingediend_op' => now(),
            ]);
        } else {
            // Geen openstaande, maak nieuwe betaling
            Betaling::create([
                'lid_id' => $lid->lid_id,
                'bedrag' => $lid->MaandelijkseBijdrage(),
                'status' => 'in_afwachting',
                'maand' => now()->month,
                'jaar' => now()->year,
                'betaling_bewijs' => $pad,
                'ingediend_op' => now(),
            ]);
        }

        // Admins op de hoogte brengen
        $admins = Gebruiker::whereHas('rollen', function ($q) {
            $q->whereIn('naam', ['Administratie Medewerker', 'Applicatie Beheerder']);
        })->get();

        foreach ($admins as $admin) {
            Notificatie::create([
                'gebruiker_id' => $admin->gebruiker_id,
                'lid_id'       => $lid->lid_id,
                'Notif_type'   => 'Betaling_ingediend',
                'titel'        => 'Lid ' . $lid->gebruiker->naam . ' heeft een betalingsbewijs geüpload.',
                'gelezen'      => false,
                'gestuurd_op'  => now(),
            ]);
        }

        \App\Models\Activiteit::log(Auth::id(), 'bewijs_geüpload', [
            'lid_id'  => $lid->lid_id,
            'pad'     => $pad,
            'details' => 'Lid ' . $lid->gebruiker->naam . ' heeft betalingsbewijs geüpload: ' . basename($pad),
        ]);

        return redirect()->back()->with('success', 'Uw betalingsbewijs is succesvol verzonden naar de beheerder ter beoordeling. U ontvangt bericht zodra het is verwerkt.');
    }

    // Lid maken voor bestaande gebruiker (Koppel als lid)
    public function store(Request $request)
    {
        Gate::authorize('leden-beheren');

        $request->validate([
            'telefoonnummer' => 'required|string',
            'adres'          => 'required|string',
            'woonplaats'     => 'required|string',
            'geboortedatum'  => 'required|date',
            'lid_type'       => 'required|in:Actief,Passief,Bijzonder',
            'lid_sinds'      => 'required|date',
            'gebruiker_id'   => 'required|exists:gebruikers,gebruiker_id',
        ]);

        $lid = Lid::create([
            'telefoonnummer' => $request->telefoonnummer,
            'adres'          => $request->adres,
            'woonplaats'     => $request->woonplaats,
            'geboortedatum'  => $request->geboortedatum,
            'lid_type'       => $request->lid_type,
            'lid_sinds'      => $request->lid_sinds,
            'gebruiker_id'   => $request->gebruiker_id,
        ]);

        if (auth()->check()) {
            \App\Models\Activiteit::log(auth()->id(), 'lid_aangemaakt', [
                'lid_id'   => $lid->lid_id,
                'details'  => 'Lid succesvol aangemaakt.',
            ]);
        }

        return redirect()->route('GebruikersBeheer')->with('success', 'Lid succesvol aangemaakt.');
    }

    // Pagina om gebruiker te koppelen als lid
    public function KoppelOfEdit($gebruiker_id)
    {
        Gate::authorize('leden-beheren');
        $gebruiker = Gebruiker::findOrFail($gebruiker_id);
        // $lid is null als er nog geen lid bestaat
        $lid = $gebruiker->lid;

        return view('editLidPagina', compact('gebruiker', 'lid'));
    }
}
