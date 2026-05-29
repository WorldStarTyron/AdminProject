<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PasswordResetController extends Controller
{
    //// Show Recover Password form
    public function show()
    {
        return view('ForgetPassword.Recover-password');
    }

    public function store(Request $request)
{
    // 1. Valideer de e-mail
    $request->validate([
        'email' => 'required|email|exists:gebruikers,email'
    ]);

    $email = $request->email;

    // 2. Genereer veilige code
    $code = rand(100000, 999999);

    // 3. Update gebruiker record met code en expiry
    DB::table('gebruikers')
        ->where('email', $email)
        ->update([
            'reset_code' => $code,
            'reset_code_expires_at' => now()->addMinutes(10) // 10 minuten geldig
        ]);

    // 4. Stuur e-mail
    Mail::to($email)->send(new ResetPasswordMail($code));

    // 5. Redirect naar verificatiepagina
    return redirect()->route('Verifycode')
        ->with('success', 'De code is verstuurd naar je e-mail.');
}

    // Show verify code page
    public function verifyCode()
    {
        return view('ForgetPassword.Verifycode');
    }

    // Show new password page
    public function newPassword()
    {
        return view('ForgetPassword.new-password');
    }

}
