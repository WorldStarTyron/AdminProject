<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Betaling;
use App\Models\Lid;
use Carbon\Carbon;

// Dagelijkse cron: openstaande betalingen omzetten en nieuwe maand voorbereiden
class CheckOpenstaandeBetalingen extends Command
{
    protected $signature = 'betalingen:check-openstaand';

    protected $description = 'Zet verlopen openstaande betalingen om naar niet_betaald en maakt nieuwe betalingen aan';

    public function handle()
    {
        $vandaag = Carbon::now();

        // Laatste week van de maand = dag 24 of later
        $inLaatsteWeek = $vandaag->day >= 24;

        $this->info('Betalingen check gestart op ' . $vandaag->format('d-m-Y'));
        $this->info('In laatste week van de maand: ' . ($inLaatsteWeek ? 'Ja' : 'Nee'));

        // Deel 1: verlopen betalingen
        $openstaandeBetalingen = Betaling::where('status', 'Openstaand')->get();

        foreach ($openstaandeBetalingen as $betaling) {
            // Deadline = einde van de maand
            $deadline = Carbon::createFromDate($betaling->jaar, $betaling->maand, 1)->endOfMonth();

            if ($vandaag->greaterThan($deadline)) {
                $betaling->status = 'niet_betaald';
                $betaling->save();

                $this->info('Betaling #' . $betaling->betaling_id . ' omgezet naar niet_betaald');

                // Check of er al een betaling is voor deze maand
                $bestaatHuidigeMaand = Betaling::where('lid_id', $betaling->lid_id)
                    ->where('maand', $vandaag->month)
                    ->where('jaar', $vandaag->year)
                    ->exists();

                if (!$bestaatHuidigeMaand) {
                    $lid = Lid::find($betaling->lid_id);

                    Betaling::create([
                        'lid_id'       => $betaling->lid_id,
                        'maand'        => $vandaag->month,
                        'jaar'         => $vandaag->year,
                        'bedrag'       => $lid ? $lid->MaandelijkseBijdrage() : $betaling->bedrag,
                        'status'       => 'Openstaand',
                        'ingediend_op' => null,
                    ]);

                    $this->info('Nieuwe betaling aangemaakt voor huidige maand voor lid #' . $betaling->lid_id);
                }
            }
        }

        // Deel 2: alvast volgende maand klaarzetten
        if ($inLaatsteWeek) {
            $this->info('Laatste week check: openstaande betalingen aanmaken voor volgende maand...');

            $volgendeMaand = $vandaag->copy()->addMonth();

            // 'betaald' en 'goed_gekeurd' tellen allebei als betaald
            $betaaldeBetalingen = Betaling::whereIn('status', ['betaald', 'goed_gekeurd'])
                ->where('maand', $vandaag->month)
                ->where('jaar', $vandaag->year)
                ->get();

            foreach ($betaaldeBetalingen as $betaling) {
                // Voorkom dubbele aanmaak
                $bestaatVolgendeMaand = Betaling::where('lid_id', $betaling->lid_id)
                    ->where('maand', $volgendeMaand->month)
                    ->where('jaar', $volgendeMaand->year)
                    ->exists();

                if (!$bestaatVolgendeMaand) {
                    $lid = Lid::find($betaling->lid_id);

                    Betaling::create([
                        'lid_id'       => $betaling->lid_id,
                        'maand'        => $volgendeMaand->month,
                        'jaar'         => $volgendeMaand->year,
                        'bedrag'       => $lid ? $lid->MaandelijkseBijdrage() : $betaling->bedrag,
                        'status'       => 'Openstaand',
                        'ingediend_op' => null,
                    ]);

                    $this->info('Volgende maand betaling aangemaakt voor lid #' . $betaling->lid_id);
                }
            }
        }

        $this->info('Betalingen check afgerond.');

        return Command::SUCCESS;
    }
}
