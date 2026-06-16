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

    // Openstaande balans (niet_betaald + Openstaand)
    $openstaandeBalans = \App\Models\Betaling::where('lid_id', $lid->lid_id)
        ->whereIn('status', ['niet_betaald', 'Openstaand'])
        ->sum('bedrag');

    // Laatste betaalde betaling
    $laatsteBetaling = \App\Models\Betaling::where('lid_id', $lid->lid_id)
        ->where('status', 'betaald')
        ->orderBy('ingediend_op', 'desc')
        ->first();

    // Zoek alleen een OPENSTAANDE betaling
    $UpcomingBetaling = \App\Models\Betaling::where('lid_id', $lid->lid_id)
        ->where('status', 'Openstaand')
        ->orderBy('jaar', 'desc')
        ->orderBy('maand', 'desc')
        ->first();

    if ($UpcomingBetaling) {
        $deadline = \Carbon\Carbon::createFromDate(
            $UpcomingBetaling->jaar,
            $UpcomingBetaling->maand, 1
        )->endOfMonth();

        $UpcomingKost = $UpcomingBetaling->bedrag;
    } else {
        $UpcomingBetaling = \App\Models\Betaling::where('lid_id', $lid->lid_id)
            ->where('status', 'betaald')
            ->orderBy('jaar', 'desc')
            ->orderBy('maand', 'desc')
            ->first();

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

    // Filter op maand en jaar
    $maandFilter = $request->input('maand'); // bijv. "03"
    $jaarFilter  = $request->input('jaar');  // bijv. "2025"

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

    return view('lidpagina', compact(
        'lid',
        'betalingen',
        'openstaandeBalans',
        'laatsteBetaling',
        'UpcomingBetaling',
        'deadline',
        'UpcomingKost',
        'beschikbareJaren'
    ));
}

    // Lid heractiveren
  public function heractiveer($gebruiker_id)
{
    Gate::authorize('gebruikersbeheer');

    $gebruiker = Gebruiker::findOrFail($gebruiker_id);
    $gebruiker->status = 'Actief';
    $gebruiker->save();

    if (auth()->check()) {
        \App\Models\Activiteit::log(auth()->id(), 'lid_gewijzigd', [
            'lid_id'  => $gebruiker->gebruiker_id,
            'details' => 'Gebruiker ' . $gebruiker->naam . ' is succesvol hergeactiveerd.',
        ]);
    }

    // ✅ Consistent met deactiveer() — redirect in plaats van JSON
    return redirect()->back()->with('success', 'Account is succesvol geheractiveerd.');
}

    // Lid deactiveren
    public function deactiveer($lid_id)
    {
        $lid = Lid::findOrFail($lid_id);
        $lid->gebruiker->status = 'Inactief';
        $lid->gebruiker->save();

        // Activiteit loggen
        if (auth()->check()) {
            \App\Models\Activiteit::log(auth()->id(), 'lid_gewijzigd', [
                'lid_id'  => $lid->lid_id,
                'details' => 'Lid ' . $lid->gebruiker->naam . ' is succesvol gedeactiveerd.',
            ]);
        }

        return redirect()->back()->with('success', 'Account is succesvol gedeactiveerd.');
    }

  public function UploadBewijs(Request $request)
  {
      $request->validate([
          'betaling_bewijs' => 'required|file|mimes:pdf|max:5120', // max 5MB
      ]);

      // Get current logged-in lid
      $lid = Lid::where('gebruiker_id', Auth::id())->firstOrFail();

      $bewijs = $request->file('betaling_bewijs');
      $pad = $bewijs->store('bewijs', 'public');

      // Check if there is an outstanding or rejected payment (status Openstaand or niet_betaald)
      $betaling = Betaling::where('lid_id', $lid->lid_id)
          ->whereIn('status', ['Openstaand', 'niet_betaald'])
          ->orderBy('jaar', 'asc')
          ->orderBy('maand', 'asc')
          ->first();

      if ($betaling) {
          $betaling->update([
              'betaling_bewijs' => $pad,
              'status' => 'in_afwachting',
              'ingediend_op' => now(),
          ]);
      } else {
          // If no openstaand betaling exists, create a new one
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

      // Notify admins
      $admins = Gebruiker::whereHas('rollen', function($q) {
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

      // Log the upload activity
      \App\Models\Activiteit::log(Auth::id(), 'bewijs_geüpload', [
          'lid_id'  => $lid->lid_id,
          'pad'     => $pad,
          'details' => 'Lid ' . $lid->gebruiker->naam . ' heeft betalingsbewijs geüpload: ' . basename($pad),
      ]);

      return redirect()->back()->with('success', 'Uw betalingsbewijs is succesvol verzonden naar de beheerder ter beoordeling. U ontvangt bericht zodra het is verwerkt.');
  }

   public function store(Request $request)
   {
        Gate::authorize('leden-beheren');

        $request->validate([
        'naam'           => 'required|string|max:255',
        'email'          => 'required|email',
        'telefoonnummer' => 'required|string',
        'adres'          => 'required|string',
        'woonplaats'     => 'required|string',
        'geboortedatum'  => 'required|date',
        'lid_type'       => 'required|in:Actief,Passief,Bijzonder',
        'lid_sinds'      => 'required|date',
        'gebruiker_id'   => 'required|exists:gebruikers,gebruiker_id',
        ]);

        Lid::create([
            'naam' => $request->naam,
            'email' => $request->email,
            'telefoonnummer' => $request->telefoonnummer,
            'adres' => $request->adres,
            'woonplaats' => $request->woonplaats,
            'geboortedatum' => $request->geboortedatum,
            'lid_type' => $request->lid_type,
            'lid_sinds' => $request->lid_sinds,
            'gebruiker_id' => $request->gebruiker_id,
        ]);

        return redirect()->route('GebruikersPagina')->with('success', 'Lid succesvol aangemaakt.');
   }





 public function KoppelOfEdit($gebruiker_id)
    {
        Gate::authorize('leden-beheren');
        $gebruiker = Gebruiker::findOrFail($gebruiker_id);
        $lid = $gebruiker->lid; // Null als er geen lid is

        return view ('editLidPagina', compact('gebruiker', 'lid'));
    }




}