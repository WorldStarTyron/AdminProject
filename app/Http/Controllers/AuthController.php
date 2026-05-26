<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Gebruiker;

class AuthController extends Controller
{
 
// show login form
public function showLogin()
{
    return view('login');
}


    //login
    public function login(Request $request)
     {
        $credentials =[
            'email' => $request->email,
            'password' => $request->password,
        ];

       if (Auth::attempt($credentials)) {
    $user = Auth::user();

    // ✅ Eerst checken of account actief is
    if ($user->actief == 0) {
        Auth::logout();
        return redirect()->route('login')
            ->with('error', 'Je account is gedeactiveerd.');
    }

    // ✅ Dan pas doorsturen
    return redirect()->route('dashboard')
        ->with('success', 'Welkom ' . $user->naam);
    }

        return redirect()->route('login')->with('error', 'Ongeldige inloggegevens');
     }

      
     public function redirectBasedOnRole()
    {
    $user = Auth::user();

    // Als rol 1 -> lidpagina
    if ($user->rol == 2) {
        return redirect()->route('lidpagina');
    }

    // Als rol 2 -> dashboard
    if ($user->rol == 1) {
        return redirect()->route('dashboard');
    }

    if ($user->rol == 3) {
        return redirect()->route('dashboard');
    }

    if ($user->rol == 4) {
        return redirect()->route('dashboard');
    }

    // Standaard fallback
    return redirect()->route('dashboard');
}


// ----------------------Logout----------------------
  public function logout (Request $request)
  {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect()->route('login');
  } 

  
}
