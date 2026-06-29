<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Dagelijks om 00:30 -> deactiveer leden die 3 maanden niet betaald hebben
        $schedule->command('app:check-deactiveer-leden')->dailyAt('00:30');

        // Dagelijks om 00:00 -> verlopen openstaande betalingen + nieuwe maand voorbereiden
        $schedule->command('betalingen:check-openstaand')->daily();

    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
   
    protected $routeMiddleware = [
        'checkrole' => \App\Http\Middleware\CheckRole::class,
    ];



}
