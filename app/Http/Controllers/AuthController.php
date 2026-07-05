<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Gebruiker;
use App\Models\Activiteit;

// Login en logout
class AuthController extends Controller
{
    public function showLogin()
    {
        return view('login');
    }

    // Inloggen
    public function login(Request $request)
    {
        $credentials = [
            'email' => $request->email,
            'password' => $request->password,
        ];

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // Inactieve accounts mogen niet inloggen
            if ($user->status === 'Inactief') {
                Auth::logout();
                return redirect()->route('login')
                    ->with('error', 'Je account is gedeactiveerd.');
            }

            // Nieuwe sessie-id na inloggen (beveiliging tegen session fixation)
            $request->session()->regenerate();

            // Loggen nadat login is gelukt
            Activiteit::log($user->gebruiker_id, 'ingelogd', [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'details' => 'Gebruiker ' . $user->naam . ' heeft ingelogd.'
            ]);
             // redirect gebruikers naar hun pagina gebaseerd op hun rollen.
            return $this->redirectBasedOnRole();
        }

        // Kijken of het email of wachtwoord fout was
        $InlogBestaat = Gebruiker::where('email', $request->email)->exists();

      if ($InlogBestaat) {
    //  wachtwoord was fout
    return redirect()->route('login')
        ->withErrors(['password' => 'Het wachtwoord is onjuist.'])
        ->withInput($request->only('email'));
} else {
    // Email bestaat niet
    return redirect()->route('login')
        ->withErrors(['email' => 'Geen account gevonden met dit email adress.'])
        ->withInput($request->only('email'));
}
    }

    // Naar de juiste pagina op basis van rol
    public function redirectBasedOnRole()
    {
        $user = Auth::user();
        $user->load('rollen');

        // Medewerkers en beheerders eerst, anders komt iemand met 2 rollen op de ledenpagina
        if ($user->hasAnyRole(['Administratie Medewerker', 'Voorzitter', 'Applicatie Beheerder'])) {
            return redirect()->route('MainDashboardPagina');
        }

        if ($user->rollen->contains('naam', 'Lid')) {
            return redirect()->route('GegevensPagina')
                ->with('success', 'Welkom ' . $user->naam);
        }

        // Geen rol: uitloggen, anders komt de gebruiker op een 403 pagina terecht
        Auth::logout();
        return redirect()->route('login')
            ->with('error', 'Je account heeft nog geen rol. Neem contact op met de beheerder.');
    }

    // Uitloggen
    public function logout(Request $request)
    {
        $user = Auth::user();

        // Loggen VOOR Auth::logout(), anders is user null
        if ($user) {
            Activiteit::log($user->gebruiker_id, 'uitgelogd', [
                'ip' => $request->ip(),
                'details' => 'Gebruiker ' . $user->naam . ' heeft uitgelogd.'
            ]);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
