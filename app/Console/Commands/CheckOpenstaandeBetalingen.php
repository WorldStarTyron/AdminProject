<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Betaling;
use App\Models\Lid;
use Carbon\Carbon;

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

        // -------------------------------------------------------
        // DEEL 1: Verlopen openstaande betalingen afhandelen
        // Openstaande betalingen waarvan de deadline voorbij is
        // worden omgezet naar niet_betaald en een nieuwe betaling
        // voor de huidige maand wordt aangemaakt.
        // -------------------------------------------------------
        $openstaandeBetalingen = Betaling::where('status', 'Openstaand')->get();

        foreach ($openstaandeBetalingen as $betaling) {

            // Deadline = laatste dag van de maand waarvoor de betaling geldt
            $deadline = Carbon::createFromDate($betaling->jaar, $betaling->maand, 1)->endOfMonth();

            if ($vandaag->greaterThan($deadline)) {

                // Zet status om naar niet_betaald
                $betaling->status = 'niet_betaald';
                $betaling->save();

                $this->info('Betaling #' . $betaling->betaling_id . ' omgezet naar niet_betaald');

                // Check of er al een openstaande betaling bestaat voor de huidige maand
                $bestaatHuidigeMaand = Betaling::where('lid_id', $betaling->lid_id)
                    ->where('maand', $vandaag->month)
                    ->where('jaar', $vandaag->year)
                    ->exists();

                if (!$bestaatHuidigeMaand) {
                    $lid = Lid::find($betaling->lid_id);

                    // Maak nieuwe openstaande betaling aan voor de huidige maand
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

        // -------------------------------------------------------
        // DEEL 2: Volgende maand betaling aanmaken
        // Alleen in de laatste week van de maand (dag >= 24).
        // Als een lid al betaald heeft deze maand, maak dan alvast
        // een openstaande betaling aan voor de volgende maand.
        // -------------------------------------------------------
        if ($inLaatsteWeek) {

            $this->info('Laatste week check: openstaande betalingen aanmaken voor volgende maand...');

            $volgendeMaand = $vandaag->copy()->addMonth();

            // Haal alle betaalde betalingen op van de huidige maand
            $betaaldeBetalingen = Betaling::where('status', 'betaald')
                ->where('maand', $vandaag->month)
                ->where('jaar', $vandaag->year)
                ->get();

            foreach ($betaaldeBetalingen as $betaling) {

                // Check of er al een betaling bestaat voor volgende maand
                $bestaatVolgendeMaand = Betaling::where('lid_id', $betaling->lid_id)
                    ->where('maand', $volgendeMaand->month)
                    ->where('jaar', $volgendeMaand->year)
                    ->exists();

                if (!$bestaatVolgendeMaand) {
                    $lid = Lid::find($betaling->lid_id);

                    // Maak nieuwe openstaande betaling aan voor volgende maand
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