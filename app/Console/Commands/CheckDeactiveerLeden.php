<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CheckDeactiveerLeden extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-deactiveer-leden';
  

    /**
     * The console command description.
     *
     * @var string
     */
        protected $description = 'Deactiveer leden die 3 Maanden achter elkaar niet hebben betaald';

    /**
     * Execute the console command.
     */
    public function handle()
    {
          // 
           $gebruikers = Gebruiker::where('status', 'Actief')
           ->whereHas('lid')
           ->get();

           foreach ($gebruikers as $gebruiker){
            $lid = $gebruiker->lid;

            // Tel hoeveel van de laatste 3 maanden niet betaald zijn
            $nietBetaaldAantal = 0;
             
         
            for ($i = 1; $i <= 2; $i++){ // Bijv: Juni, mei, april
                $HeeftBetaald = Betaling::where('lid_id', $lid->lid_id)
                ->where('maand', now()->subMonths($i)->month)
                ->where('jaar', now()->subMonths($i)->year)
                ->where('status','betaald')
                ->exists();
               

                if (!$heeftBetaald){
                    $nietBetaaldAantal++;
                }
             }


               // 3 Maanden niet betaald =  acount deactiveren
               if ($nietBetaaldAantal === 3){
                $gebruiker->status = 'Inactief';
                $gebruiker->save();

                $this->info("Gedeactiveerd: {$gebruiker->naam}");
               }
           }

           this->info('Klaar met controleren');
    }
}
