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
        // Elke 1e van de maand om 00:00 -> check of leden betaald hebben
        $schedule->command('subscriptie:check')->monthlyOn(1, '00:00');
        $schedule->command('check:deactiveer-leden')->daily();
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
