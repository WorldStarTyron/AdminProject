<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Betaling;
use Illuminate\Support\Facades\Gate;

class RapportController extends Controller
{
    public function index()
    {
        return view('RapportPagina');
    }


    //Rapportage Pagina Data
    public function RapportageData(Request $request)
    {
         Gate::authorize('rapport-bekijken'); 
         
        // We zoeken de oudste en nieuwste betaling in de database
        $oudsteDatum = Betaling::min('ingediend_op');
        $nieuwsteDatum = Betaling::max('ingediend_op');

        // We pakken de datums die zijn gekozen, of gebruiken de oudste en nieuwste betaling
        $from = $request->input('from', $oudsteDatum ? date('Y-m-d', strtotime($oudsteDatum)) : date('Y-01-01'));
        $to = $request->input('to', $nieuwsteDatum ? date('Y-m-d', strtotime($nieuwsteDatum)) : date('Y-m-d'));

        // We maken de filterdatums inclusief voor het begin en einde van de dag
        $fromStart = $from . ' 00:00:00';
        $toEnd = $to . ' 23:59:59';

        // We filteren de betalingen op de gekozen datums
        $query = Betaling::query()->whereBetween('ingediend_op', [$fromStart, $toEnd]);


        
        // Card 1: Totale inkomsten (som van alle betaalde betalingen)
        $totaleInkomsten = (clone $query)->where('status', 'betaald')->sum('bedrag');

        // Parse date range to months and years for outstanding payments (where ingediend_op is null)
        $startYear = (int) date('Y', strtotime($from));
        $startMonth = (int) date('m', strtotime($from));
        $endYear = (int) date('Y', strtotime($to));
        $endMonth = (int) date('m', strtotime($to));

        // Card 2: Openstaand bedrag (som van niet-betaalde + openstaande betalingen belonging to the period's months)
        $openstaandBedrag = Betaling::whereIn('status', ['niet_betaald', 'Openstaand'])
            ->whereRaw('(jaar * 12 + maand) >= ?', [$startYear * 12 + $startMonth])
            ->whereRaw('(jaar * 12 + maand) <= ?', [$endYear * 12 + $endMonth])
            ->sum('bedrag');

        $openstaandAantal = Betaling::whereIn('status', ['niet_betaald', 'Openstaand'])
            ->whereRaw('(jaar * 12 + maand) >= ?', [$startYear * 12 + $startMonth])
            ->whereRaw('(jaar * 12 + maand) <= ?', [$endYear * 12 + $endMonth])
            ->distinct('lid_id')
            ->count('lid_id');

        // Als er wel openstaande betalingen zijn maar het geregistreerde bedrag is 0,
        // schatten we 150 SRD per lid
        if ($openstaandBedrag == 0 && $openstaandAantal > 0) {
            $openstaandBedrag = $openstaandAantal * 150;
        }

        // We berekenen het totale verwachte bedrag en het betaalpercentage (dekkingsgraad)
        $totaalVerwacht = $totaleInkomsten + $openstaandBedrag;
        $dekkingsgraad = $totaalVerwacht > 0 ? round(($totaleInkomsten / $totaalVerwacht) * 100, 1) : 100;

        // We berekenen de echte omzetverdeling per betaalmethode binnen de geselecteerde periode
        $overmakingBedrag = (clone $query)->where('status', 'betaald')->where('methode', 'overmaking')->sum('bedrag');
        $fysiekBedrag = (clone $query)->where('status', 'betaald')->where('methode', 'fysiek')->sum('bedrag');
        $totaalBedragMethoden = $overmakingBedrag + $fysiekBedrag;
 

        //Berekening van de dekkingsgraad op basis van overmakingen en fysieke betalingen.
        if ($totaalBedragMethoden > 0) {
            $overmakingPercentage = round(($overmakingBedrag / $totaalBedragMethoden) * 100);
            $fysiekPercentage = 100 - $overmakingPercentage;
        } else {
            $overmakingPercentage = 50;
            $fysiekPercentage = 50;
        }

        // Card 3: Totaal leden
        $totaalLeden = \App\Models\Lid::count();

        // Recente betalingen tabel (laatste 10)
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
                    'in_behandeling' => 'In behandeling',
                    default          => ucfirst($b->status),
                };
                return $b;
            });

        // We maken de datums netjes voor de grafiek
        try {
            $fromCarbon = \Carbon\Carbon::parse($from)->startOfMonth();
            $toCarbon = \Carbon\Carbon::parse($to)->startOfMonth();
        } catch (\Exception $e) {
            $fromCarbon = now()->subMonths(6)->startOfMonth();
            $toCarbon = now()->startOfMonth();
        }

        // We bouwen een lijst met alle maanden die tussen de datums liggen
        $maanden = collect();
        if ($fromCarbon->lte($toCarbon)) {
            $huidige = $fromCarbon->copy();
            // Maximaal 12 maanden in de grafiek om het netjes te houden
            $maxMaanden = 12;
            while ($huidige->lte($toCarbon) && $maanden->count() < $maxMaanden) {
                $maanden->push([
                    'jaar'  => (int) $huidige->format('Y'),
                    'maand' => (int) $huidige->format('n'),
                    'label' => ucfirst($huidige->translatedFormat('M')), // Maandnaam zonder jaartal
                ]);
                $huidige->addMonth();
            }
        }

        // We halen de inkomsten op per maand en per betaalmethode
        $rijen = Betaling::selectRaw('jaar, maand, methode, SUM(bedrag) as totaal')
            ->where('status', 'betaald')
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

        // Calculate real new members this quarter
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
