<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Gebruiker;
use App\Models\Betaling;
use Carbon\Carbon;

// Cron: deactiveer leden die 3 maanden achter elkaar niet hebben betaald
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

            // Skip 1: nieuwe leden die nog geen 3 maanden lid zijn
            if ($lid->lid_sinds && Carbon::parse($lid->lid_sinds)->diffInMonths(now()) < 3) {
                continue;
            }

            // Skip 2: heeft deze maand al betaald → niet deactiveren
            $betaaldDezeMaand = Betaling::where('lid_id', $lid->lid_id)
                ->where('maand', now()->month)
                ->where('jaar', now()->year)
                ->whereIn('status', ['betaald', 'goed_gekeurd'])
                ->exists();

            if ($betaaldDezeMaand) {
                continue;
            }

            // Tel hoeveel van de vorige 3 maanden niet betaald zijn
            $nietBetaaldAantal = 0;

            for ($i = 1; $i <= 3; $i++) {
                $heeftBetaald = Betaling::where('lid_id', $lid->lid_id)
                    ->where('maand', now()->subMonths($i)->month)
                    ->where('jaar', now()->subMonths($i)->year)
                    ->whereIn('status', ['betaald', 'goed_gekeurd'])
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

        $this->info('Klaar met controleren');
    }
}
