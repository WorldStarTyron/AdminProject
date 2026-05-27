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
    // De rol checken en de juiste pagina teruggeven
    return $this->redirectBasedOnRole();

    }   
    return redirect()->route('login')->with('error', 'Ongeldige inloggegevens');
    }  
    public function redirectBasedOnRole()
    {
        $user = Auth::user();
        
        // Zorg dat de rollen van de gebruiker geladen zijn
        $user->load('rollen');

        // Check of de gebruiker de rol 'Lid' heeft
        if ($user->rollen->contains('naam', 'Lid')) {
            return redirect()->route('GegevensPagina')
                ->with('success', 'Welkom ' . $user->naam); 
        }

        // Alle andere rollen (Beheerders, Voorzitter, etc.) gaan naar het dashboard
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
