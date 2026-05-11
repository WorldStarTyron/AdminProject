<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    //login
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            if ($user->is_admin == 1) {
                // Admin user - redirect to admin dashboard
                return redirect()->route('admin.dashboard')
                    ->with('success', 'Welkom bij het beheerdersdashboard!');
            } else {
                // Regular user - redirect to regular dashboard
                Auth::logout();
                return redirect()->route('login')
                    ->with('error', 'Je hebt geen toegang tot het beheerdersdashboard. Dit is een beveiligde pagina voor beheerders.');
            }
        }

        // Authentication failed
        return redirect()->route('login')
            ->with('error', 'Ongeldige inloggegevens. Probeer opnieuw.');
    }

    // In je AuthController.php
public function logout(Request $request)
{
    Auth::logout();
    return redirect()->route('login');
}
}
