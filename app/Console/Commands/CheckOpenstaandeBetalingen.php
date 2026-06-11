<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Betaling;
use App\Models\Lid;
use Carbon\Carbon;

class CheckOpenstaandeBetalingen extends Command
{
    protected $signature = 'betalingen:check-openstaand';

    protected $description = 'Zet verlopen "Openstaand" betalingen om naar "niet betaald"';

    public function handle()
    {
        $vandaag = Carbon::now();

        $openstaandebetalingen = Betaling::where('status', 'Openstaand')->get();

        foreach ($openstaandebetalingen as $betaling) {
            // Deadline = eind van de maand waarvoor de betaling geldt
            $deadline = Carbon::createFromDate($betaling->jaar, $betaling->maand, 1)->endOfMonth();

            if ($vandaag->greaterThan($deadline)) {
                // Status naar niet betaald
                $betaling->status = 'niet_betaald';
                $betaling->save();

                $this->info('Betaling #' . $betaling->betaling_id . ' is gewijzigd naar niet_betaald');

                // Check of er al een betaling bestaat voor de HUIDIGE maand
                $CheckBetaling = Betaling::where('lid_id', $betaling->lid_id)
                    ->where('maand', $vandaag->month)
                    ->where('jaar', $vandaag->year)
                    ->exists();

                if (!$CheckBetaling) {
                    $lid = Lid::find($betaling->lid_id);

                    Betaling::create([
                        'lid_id'       => $betaling->lid_id,
                        'maand'        => $vandaag->month,
                        'jaar'         => $vandaag->year,
                        'bedrag'       => $lid ? $lid->MaandelijkseBijdrage() : $betaling->bedrag,
                        'status'       => 'Openstaand',
                        'ingediend_op' => null,
                    ]);

                    $this->info('Nieuwe openstaande betaling aangemaakt voor lid #' . $betaling->lid_id);
                }
            }
        }

        return Command::SUCCESS;
    }
}
