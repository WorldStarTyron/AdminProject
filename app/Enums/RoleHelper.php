<?php

use App\Enums\Role;

if (!function_exists('hasRole')) {
    function hasRole($roleId)
    {
        return auth()->check() && auth()->user()->rol == $roleId;
    }
}

if (!function_exists('isAdmin')) {
    function isAdmin()
    {
        return hasRole(Role::administratieMedewerker);
    }
}

if (!function_exists('isLid')) {
    function isLid()
    {
        return hasRole(Role::Lid);
    }
}

if (!function_exists('isVoorzitter')) {
    function isVoorzitter()
    {
        return hasRole(Role::Voorzitter);
    }
}

if (!function_exists('isApplicatieBeheerder')) {
    function isApplicatieBeheerder()
    {
        return hasRole(Role::ApplicatieBeheerder);
    }
}



function redirectBasedOnRole($user)
{
    return match($user->rol) {
        Role::Lid => redirect()->route('GegevensPagina'),
        default   => redirect()->route('MainDashboardPagina'),
    };
}



