<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Enums\Role;

// Hier staan alle Gates (permissies)
class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [];

    public function boot(): void
    {
        // Dashboard
        Gate::define('dashboard', fn ($user) =>
            $user->hasAnyRole(['Administratie Medewerker', 'Voorzitter', 'Applicatie Beheerder'])
        );

        // Alleen leden
        Gate::define('eigen-profiel', fn ($user) =>
            $user->isLid()
        );

        // Leden
        Gate::define('leden-bekijken', fn ($user) =>
            $user->hasAnyRole(['Administratie Medewerker', 'Voorzitter', 'Applicatie Beheerder'])
        );

        Gate::define('leden-beheren', fn ($user) =>
            $user->hasAnyRole(['Administratie Medewerker', 'Applicatie Beheerder'])
        );

        // Betalingen
        Gate::define('betalingen-bekijken', fn ($user) =>
            $user->hasAnyRole(['Administratie Medewerker', 'Voorzitter', 'Applicatie Beheerder'])
        );

        Gate::define('betalingen-beheren', fn ($user) =>
            $user->hasAnyRole(['Administratie Medewerker', 'Applicatie Beheerder'])
        );

        // Verwijderen alleen voor app beheerder
        Gate::define('leden-verwijderen', fn ($user) =>
            $user->hasAnyRole(['Applicatie Beheerder'])
        );

        Gate::define('betalingen-verwijderen', fn ($user) =>
            $user->isApplicatieBeheerder()
        );

        // Rapport en log
        Gate::define('rapport-bekijken', fn ($user) =>
            $user->hasAnyRole(['Applicatie Beheerder', 'Voorzitter'])
        );

        Gate::define('activiteitlog-bekijken', fn ($user) =>
            $user->hasAnyRole(['Applicatie Beheerder'])
        );

        // Beheer pagina's
        Gate::define('rollenbeheer', fn ($user) => $user->isApplicatieBeheerder());
        Gate::define('gebruikersbeheer', fn ($user) => $user->isApplicatieBeheerder());

        Gate::define('leden-heractiveren', fn ($user) =>
            $user->hasAnyRole(['Administratie Medewerker', 'Applicatie Beheerder'])
        );

        Gate::define('leden-Deactiveren', fn($user) =>
            $user->hasAnyRole(['Administratie Medewerker', 'Applicatie Beheerder'])
        );
    }
}