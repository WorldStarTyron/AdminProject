<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use App\Mail\ResetPasswordMail;

class PasswordResetController extends Controller
{
    // Show Recover Password form
    public function show()
    {
        return view('ForgetPassword.Recover-password');
    }

    public function sendResetCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:gebruikers,email'
        ]);

        $email = $request->email;
        $code = rand(100000, 999999); // secure code with 6 digits

        // Get the gebruiker_id for this email
        $gebruiker = DB::table('gebruikers')->where('email', $email)->first();

        // Delete any existing unused reset codes for this user
        DB::table('wachtwoord_reset')
            ->where('gebruiker_id', $gebruiker->gebruiker_id)
            ->where('gebruikt', 0)
            ->delete();

        DB::table('wachtwoord_reset')->insert([
            'gebruiker_id' => $gebruiker->gebruiker_id,
            'token' => $code,
            'verlopen_op' => now()->addMinutes(10),
            'gebruikt' => 0,
        ]);

        Mail::to($email)->send(new ResetPasswordMail($code, $email));

        // Store the email in session so we can use it during verification
        session(['reset_email' => $email]);

        return redirect()->route('verify-code')
            ->with('success', 'De code is verstuurd naar je e-mail.');
    }

    public function verifyCode()
    {
        return view('ForgetPassword.Verifycode');
    }

    public function postVerifyCode(Request $request)
    {
        $request->validate([
            'code' => 'required|array|size:6',
            'code.*' => 'required|numeric|digits:1',
        ]);

        // Combine the 6 individual digits into a single code
        $code = implode('', $request->code);

        // Get the email from session
        $email = session('reset_email');

        if (!$email) {
            return redirect()->route('recover-password')
                ->with('error', 'Sessie verlopen. Vraag een nieuwe code aan.'); // sessie verlopen
        }

        // Get the gebruiker_id for this email
        $gebruiker = DB::table('gebruikers')->where('email', $email)->first();

        if (!$gebruiker) {
            return redirect()->route('recover-password')
                ->with('error', 'Gebruiker niet gevonden.');
        }

        // Find a valid, unused, non-expired reset token
        $resetRecord = DB::table('wachtwoord_reset')
            ->where('gebruiker_id', $gebruiker->gebruiker_id)
            ->where('token', $code)
            ->where('gebruikt', 0)
            ->where('verlopen_op', '>', now())
            ->first();

        if (!$resetRecord) {
            return redirect()->route('verify-code')
                ->with('error', 'De code is onjuist of verlopen. Probeer opnieuw.');
        }

        // Mark the token as used
        DB::table('wachtwoord_reset')
            ->where('reset_id', $resetRecord->reset_id)
            ->update(['gebruikt' => 1]);

        // Store the reset_id in session to verify during password change
        session(['reset_verified' => true]);

        return redirect()->route('new-password')
            ->with('success', 'Code geverifieerd. Stel nu je nieuwe wachtwoord in.');
    }

    public function newPassword()
    {
        return view('ForgetPassword.NewPassword');
    }

    public function postNewPassword(Request $request)
    {
        $request->validate([
            'password' => 'required|min:8|confirmed',
        ]);

        // Check if the user has verified their code
        if (!session('reset_verified')) {
            return redirect()->route('recover-password')
                ->with('error', 'Verificatie niet voltooid. Begin opnieuw.');
        }

        $email = session('reset_email');

        if (!$email) {
            return redirect()->route('recover-password')
                ->with('error', 'Sessie verlopen. Begin opnieuw.');
        }

        // Get the gebruiker record to log
        $gebruiker = DB::table('gebruikers')->where('email', $email)->first();

        // Update the password in the gebruikers table
        DB::table('gebruikers')
            ->where('email', $email)
            ->update(['wachtwoord_hash' => Hash::make($request->password)]);

        if ($gebruiker) {
            \App\Models\Activiteit::log($gebruiker->gebruiker_id, 'wachtwoord_gewijzigd', [
                'email'   => $email,
                'details' => 'Wachtwoord succesvol hersteld via e-mail herstelcode verificatie.'
            ]);
        }

        // Clear all reset session data
        session()->forget(['reset_email', 'reset_verified']);

        return redirect()->route('login')
            ->with('success', 'Wachtwoord succesvol gewijzigd. Je kunt nu inloggen.');
    }
}