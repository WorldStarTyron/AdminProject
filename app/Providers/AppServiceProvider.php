<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('Layouts.Shared.leden-overzicht', function ($view) {

            $totaalLeden = DB::table('leden')->count(); //totaal aantal leden

            /*$nieuweLeden = DB::table('leden')->where('created_at', '>=', Carbon::now()->subDays(30))->count(); //leden van deze maand */
            $view->with([
                'leden' => \App\Models\Lid::all(),
                'totaalLeden' => $totaalLeden,
            ]);
        });
    }
}
