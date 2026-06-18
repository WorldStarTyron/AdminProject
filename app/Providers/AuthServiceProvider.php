<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Enums\Role;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [];

    public function boot(): void
    {
       // app/Providers/AuthServiceProvider.php

       //
Gate::define('dashboard', fn ($user) =>
$user->hasAnyRole(['Administratie Medewerker', 'Voorzitter', 'Applicatie Beheerder', 'Applicatie beheerder'])
);

Gate::define('eigen-profiel', fn ($user) =>
$user->isLid()
);

// LEDEN
Gate::define('leden-bekijken', fn ($user) =>
$user->hasAnyRole(['Administratie Medewerker', 'Voorzitter', 'Applicatie Beheerder', 'Applicatie beheerder'])
);

Gate::define('leden-beheren', fn ($user) =>
$user->hasAnyRole(['Administratie Medewerker', 'Applicatie Beheerder', 'Applicatie beheerder'])
);



// BETALINGEN
Gate::define('betalingen-bekijken', fn ($user) =>
$user->hasAnyRole(['Administratie Medewerker', 'Voorzitter', 'Applicatie Beheerder', 'Applicatie beheerder'])
);

Gate::define('betalingen-beheren', fn ($user) =>
$user->hasAnyRole(['Administratie Medewerker', 'Applicatie Beheerder', 'Applicatie beheerder'])
);

// SOFT DELETE    betalingen-verwijderen
Gate::define('leden-verwijderen', fn ($user) =>
$user->hasAnyRole(['Applicatie Beheerder', 'Applicatie beheerder'])
);

Gate::define('betalingen-verwijderen', fn ($user) =>
$user->isApplicatieBeheerder()
);



// RAPPORT & LOG
Gate::define('rapport-bekijken', fn ($user) =>
$user->hasAnyRole(['Applicatie Beheerder', 'Applicatie beheerder', "Voorzitter"])
);

Gate::define('activiteitlog-bekijken', fn ($user) =>
$user->hasAnyRole(['Applicatie Beheerder', 'Applicatie beheerder'])
);

// BEHEER
Gate::define('rollenbeheer', fn ($user) => $user->isApplicatieBeheerder());
Gate::define('gebruikersbeheer', fn ($user) => $user->isApplicatieBeheerder());



// HOUD alleen deze onderaan:
Gate::define('leden-heractiveren', fn ($user) =>
    $user->hasAnyRole(['Administratie Medewerker', 'Applicatie Beheerder', 'Applicatie beheerder'])
);

// Leden deactiveren
Gate::define('leden-Deactiveren', fn($user) =>
    $user->hasAnyRole(['Administratie Medewerker', 'Applicatie Beheerder', 'Applicatie beheerder'])
);

    }
}
