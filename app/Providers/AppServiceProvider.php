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
        // Auto-fix: delete Vite 'hot' file if uploaded to online production environment
        if (config('app.env') === 'production' || (!app()->runningInConsole() && !in_array(request()->getHost(), ['localhost', '127.0.0.1', '::1']))) {
            $hotPath = public_path('hot');
            if (file_exists($hotPath)) {
                @unlink($hotPath);
            }
        }
        View::composer('Layouts.Tables.leden-overzicht', function ($view) {

            $totaalLeden = DB::table('leden')->count(); //totaal aantal leden

            /*$nieuweLeden = DB::table('leden')->where('created_at', '>=', Carbon::now()->subDays(30))->count(); //leden van deze maand */
            $view->with([
                'leden' => \App\Models\Lid::all(),
                'totaalLeden' => $totaalLeden,
            ]);
        });
    }
}
