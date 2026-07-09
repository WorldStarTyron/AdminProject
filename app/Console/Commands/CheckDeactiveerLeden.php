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
        ->whereNull('suspension_at')
        ->whereHas('lid')
        ->get();

    foreach ($gebruikers as $gebruiker) {
        $lid = $gebruiker->lid;

        // Nieuwe leden overslaan (nog geen 3 volle maanden lid)
        if ($lid->lid_sinds && $lid->lid_sinds->diffInMonths(now()) < 3) {
            continue;
        }

        // Huidige maand betaald? → uitzondering, blijft actief
        if ($this->heeftBetaald($lid, now())) {
            continue;
        }

        // Laatste 3 opeenvolgende maanden allemaal onbetaald?
        $alleDrieOnbetaald = true;
        for ($i = 1; $i <= 3; $i++) {
            if ($this->heeftBetaald($lid, now()->subMonthsNoOverflow($i))) {
                $alleDrieOnbetaald = false;
                break;
            }
        }

        if ($alleDrieOnbetaald) {
            $gebruiker->Suspend();
            $this->info("Suspend: {$gebruiker->naam}");
        }
    }
}

private function heeftBetaald($lid, $moment): bool
{
    return Betaling::where('lid_id', $lid->lid_id)
        ->where('maand', $moment->month)
        ->where('jaar', $moment->year)
        ->whereIn('status', ['betaald', 'goed_gekeurd'])
        ->exists();
}
}
