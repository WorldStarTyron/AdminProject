<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Betaling;
use Illuminate\Support\Facades\Gate;

// Rapportage pagina met financiële cijfers
class RapportController extends Controller
{
    public function index()
    {
        return view('RapportPagina');
    }

    // Data voor de rapportage pagina
    public function RapportageData(Request $request)
    {
        Gate::authorize('rapport-bekijken');

        // Oudste en nieuwste betaling als standaard periode
        $oudsteDatum = Betaling::min('ingediend_op');
        $nieuwsteDatum = Betaling::max('ingediend_op');

        $from = $request->input('from', $oudsteDatum ? date('Y-m-d', strtotime($oudsteDatum)) : date('Y-01-01'));
        $to = $request->input('to', $nieuwsteDatum ? date('Y-m-d', strtotime($nieuwsteDatum)) : date('Y-m-d'));

        // Datums inclusief maken (00:00 tot 23:59)
        $fromStart = $from . ' 00:00:00';
        $toEnd = $to . ' 23:59:59';

        $query = Betaling::query()->whereBetween('ingediend_op', [$fromStart, $toEnd]);

        // Card 1: totale inkomsten
        $totaleInkomsten = (clone $query)->whereIn('status', ['betaald', 'goed_gekeurd'])->sum('bedrag');

        // Voor openstaande filteren we op maand+jaar (ingediend_op is dan null)
        $startYear = (int) date('Y', strtotime($from));
        $startMonth = (int) date('m', strtotime($from));
        $endYear = (int) date('Y', strtotime($to));
        $endMonth = (int) date('m', strtotime($to));

        // Card 2: openstaand bedrag
        $openstaandBedrag = Betaling::whereIn('status', ['niet_betaald', 'Openstaand'])
            ->whereRaw('(jaar * 12 + maand) >= ?', [$startYear * 12 + $startMonth])
            ->whereRaw('(jaar * 12 + maand) <= ?', [$endYear * 12 + $endMonth])
            ->sum('bedrag');

        // Distinct lid_id voor unieke tellingen
        $openstaandAantal = Betaling::whereIn('status', ['niet_betaald', 'Openstaand'])
            ->whereRaw('(jaar * 12 + maand) >= ?', [$startYear * 12 + $startMonth])
            ->whereRaw('(jaar * 12 + maand) <= ?', [$endYear * 12 + $endMonth])
            ->distinct('lid_id')
            ->count('lid_id');

        // Schatting van 150 per lid als bedrag ontbreekt
        if ($openstaandBedrag == 0 && $openstaandAantal > 0) {
            $openstaandBedrag = $openstaandAantal * 150;
        }

        // Dekkingsgraad
        $totaalVerwacht = $totaleInkomsten + $openstaandBedrag;
        $dekkingsgraad = $totaalVerwacht > 0 ? round(($totaleInkomsten / $totaalVerwacht) * 100, 1) : 100;

        // Verdeling tussen betaalmethoden
        $overmakingBedrag = (clone $query)->whereIn('status', ['betaald', 'goed_gekeurd'])->where('methode', 'overmaking')->sum('bedrag');
        $fysiekBedrag = (clone $query)->whereIn('status', ['betaald', 'goed_gekeurd'])->where('methode', 'fysiek')->sum('bedrag');
        $totaalBedragMethoden = $overmakingBedrag + $fysiekBedrag;

        if ($totaalBedragMethoden > 0) {
            $overmakingPercentage = round(($overmakingBedrag / $totaalBedragMethoden) * 100);
            $fysiekPercentage = 100 - $overmakingPercentage;
        } else {
            // Geen data, dan 50/50 tonen
            $overmakingPercentage = 50;
            $fysiekPercentage = 50;
        }

        $totaalLeden = \App\Models\Lid::count();

        // Laatste 6 betalingen voor de tabel
        $betalingen = Betaling::select('betalingen.*', 'gebruikers.naam')
            ->join('leden', 'betalingen.lid_id', '=', 'leden.lid_id')
            ->join('gebruikers', 'leden.gebruiker_id', '=', 'gebruikers.gebruiker_id')
            ->whereBetween('betalingen.ingediend_op', [$fromStart, $toEnd])
            ->orderBy('betalingen.ingediend_op', 'desc')
            ->limit(6)
            ->get()
            ->map(function ($b) {
                $b->datum = $b->ingediend_op;
                $b->status_label = match($b->status) {
                    'betaald'        => 'Betaald',
                    'niet_betaald'   => 'Niet betaald',
                    'Openstaand'     => 'Openstaand',
                    'in_afwachting'  => 'In afwachting',
                    default          => ucfirst($b->status),
                };
                return $b;
            });

        // Datums omzetten voor de grafiek
        try {
            $fromCarbon = \Carbon\Carbon::parse($from)->startOfMonth();
            $toCarbon = \Carbon\Carbon::parse($to)->startOfMonth();
        } catch (\Exception $e) {
            // Bij fout: laatste 6 maanden tonen
            $fromCarbon = now()->subMonths(6)->startOfMonth();
            $toCarbon = now()->startOfMonth();
        }

        // Maximaal 12 maanden in de grafiek
        $maanden = collect();
        if ($fromCarbon->lte($toCarbon)) {
            $huidige = $fromCarbon->copy();
            $maxMaanden = 12;
            while ($huidige->lte($toCarbon) && $maanden->count() < $maxMaanden) {
                $maanden->push([
                    'jaar'  => (int) $huidige->format('Y'),
                    'maand' => (int) $huidige->format('n'),
                    'label' => ucfirst($huidige->translatedFormat('M')),
                ]);
                $huidige->addMonth();
            }
        }

        // Inkomsten per maand per methode
        $rijen = Betaling::selectRaw('jaar, maand, methode, SUM(bedrag) as totaal')
            ->whereIn('status', ['betaald', 'goed_gekeurd'])
            ->whereBetween('ingediend_op', [$fromStart, $toEnd])
            ->groupBy('jaar', 'maand', 'methode')
            ->get()
            ->groupBy(fn($r) => $r->jaar . '-' . $r->maand)
            ->map(fn($groep) => $groep->keyBy('methode'));

        $chartLabels     = [];
        $chartFysiek     = [];
        $chartOvermaking = [];

        foreach ($maanden as $m) {
            $sleutel           = $m['jaar'] . '-' . $m['maand'];
            $chartLabels[]     = $m['label'];
            $chartFysiek[]     = (float) ($rijen[$sleutel]['fysiek']->totaal     ?? 0);
            $chartOvermaking[] = (float) ($rijen[$sleutel]['overmaking']->totaal ?? 0);
        }

        // Nieuwe leden dit kwartaal
        $nieuwDitKwartaal = \App\Models\Lid::where('lid_sinds', '>=', now()->startOfQuarter())->count();

        return view('RapportPagina', compact(
            'totaleInkomsten',
            'openstaandBedrag',
            'openstaandAantal',
            'totaalLeden',
            'nieuwDitKwartaal',
            'betalingen',
            'from',
            'to',
            'dekkingsgraad',
            'totaalVerwacht',
            'overmakingPercentage',
            'fysiekPercentage',
            'chartLabels',
            'chartFysiek',
            'chartOvermaking'
        ));
    }
}
