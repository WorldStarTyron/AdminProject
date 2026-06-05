<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Lid;
use App\Models\Betaling;
use Carbon\Carbon;

// Dit command checkt of leden hun maand betaling hebben gedaan
// Als ze niet betaald hebben, maakt het een "niet_betaald" record aan
class CheckSubscription extends Command
{
    // De naam van het command die je typt in de terminal
    protected $signature = 'subscriptie:check
                            {--maand= : Welke maand je wil checken (1-12)}
                            {--jaar= : Welk jaar je wil checken}';

    // Korte uitleg van wat dit command doet
    protected $description = 'Check of alle leden hun maand betaling hebben gedaan';

    public function handle()
    {
        // Pak de maand en jaar (of gebruik de huidige maand/jaar)
        $maand = $this->option('maand') ?? now()->month;
        $jaar  = $this->option('jaar')  ?? now()->year;

        $this->info("Subscriptie check voor: maand {$maand}, jaar {$jaar}");
        $this->info('-------------------------------------------');

        // Pak alle leden uit de database
        $leden = Lid::with('gebruiker')->get();

        // Tellers om bij te houden hoeveel er zijn verwerkt
        $alBetaald = 0;
        $nietBetaald = 0;
        $alGemarkeerd = 0;

        // Loop door elk lid
        foreach ($leden as $lid) {

            // Naam van het lid (als die er is)
            $naam = $lid->gebruiker->naam ?? "Lid #{$lid->lid_id}";

            // Check of dit lid al een betaling heeft voor deze maand
            $betaling = Betaling::where('lid_id', $lid->lid_id)
                ->where('maand', $maand)
                ->where('jaar', $jaar)
                ->first();

            // Als er al een betaling is met status "betaald" -> skip
            if ($betaling && $betaling->status === 'betaald') {
                $this->line("  ✅ {$naam} - al betaald");
                $alBetaald++;
                continue;
            }

            // Als er al een "niet_betaald" record is -> skip (niet dubbel maken)
            if ($betaling && $betaling->status === 'niet_betaald') {
                $this->line("  ⏭️  {$naam} - was al gemarkeerd als niet betaald");
                $alGemarkeerd++;
                continue;
            }

            // Als de status "Openstaand" is -> deadline is voorbij, wijzig naar niet_betaald
            if ($betaling && $betaling->status === 'Openstaand') {
                $betaling->update(['status' => 'niet_betaald']);
                $this->warn("  ❌ {$naam} - openstaand → niet_betaald (deadline voorbij)");
                $nietBetaald++;
                continue;
            }

            // Als er een betaling is met andere status (bv. in_behandeling)
            // Dan updaten we die naar "niet_betaald"
            if ($betaling) {
                $betaling->update(['status' => 'niet_betaald']);
                $this->warn("  ❌ {$naam} - status veranderd naar niet_betaald");
                $nietBetaald++;
                continue;
            }

            // Als er helemaal geen betaling is -> maak een nieuwe "niet_betaald" aan
            Betaling::create([
                'lid_id'  => $lid->lid_id,
                'bedrag'  => 0,
                'methode' => 'fysiek',
                'status'  => 'niet_betaald',
                'maand'   => $maand,
                'jaar'    => $jaar,
            ]);

            $this->warn("  ❌ {$naam} - niet betaald (nieuw record aangemaakt)");
            $nietBetaald++;
        }

        // Laat het totaal zien
        $this->newLine();
        $this->info('=========== RESULTAAT ===========');
        $this->info("Totaal leden:         " . $leden->count());
        $this->info("Al betaald:           {$alBetaald}");
        $this->info("Al gemarkeerd:        {$alGemarkeerd}");
        $this->warn("Niet betaald (nieuw): {$nietBetaald}");
        $this->info('=================================');

        return Command::SUCCESS;
    }
}
