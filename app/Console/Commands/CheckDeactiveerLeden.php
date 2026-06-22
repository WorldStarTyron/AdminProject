<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

// Cron: deactiveer leden die 3 maanden niet betaald hebben
class CheckDeactiveerLeden extends Command
{
    protected $signature = 'app:check-deactiveer-leden';

    protected $description = 'Deactiveer leden die 3 Maanden achter elkaar niet hebben betaald';

    public function handle()
    {
        $gebruikers = Gebruiker::where('status', 'Actief')
            ->whereHas('lid')
            ->get();

        foreach ($gebruikers as $gebruiker) {
            $lid = $gebruiker->lid;

            // Tellen hoeveel maanden niet betaald
            $nietBetaaldAantal = 0;

            for ($i = 1; $i <= 2; $i++) {
                $HeeftBetaald = Betaling::where('lid_id', $lid->lid_id)
                    ->where('maand', now()->subMonths($i)->month)
                    ->where('jaar', now()->subMonths($i)->year)
                    ->where('status', 'betaald')
                    ->exists();

                if (!$heeftBetaald) {
                    $nietBetaaldAantal++;
                }
            }

            // 3 maanden niet betaald = account deactiveren
            if ($nietBetaaldAantal === 3) {
                $gebruiker->status = 'Inactief';
                $gebruiker->save();

                $this->info("Gedeactiveerd: {$gebruiker->naam}");
            }
        }

        this->info('Klaar met controleren');
    }
}
