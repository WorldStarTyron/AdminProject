<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
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

        // Check if logged-in user is attempting to reset another user's password
        if (auth()->check() && strtolower($email) !== strtolower(auth()->user()->email)) {
            return back()
                ->withInput()
                ->with('error', 'U kunt alleen een verificatiecode sturen naar uw eigen e-mailadres.');
        }

        // Rate limiting: max 3 reset requests per email per 15 minutes
        $rateLimitKey = 'password-reset:' . strtolower($email);
        if (RateLimiter::tooManyAttempts($rateLimitKey, 3)) {
            $seconds = RateLimiter::availableIn($rateLimitKey);
            return back()
                ->withInput()
                ->with('error', 'Te veel pogingen. Probeer het opnieuw over ' . ceil($seconds / 60) . ' minuten.');
        }
        RateLimiter::hit($rateLimitKey, 900); // 15 minutes decay

        // Get the gebruiker_id for this email
        $gebruiker = DB::table('gebruikers')->where('email', $email)->first();



        // Generate a cryptographically secure 6-digit code
        $code = str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT);

        // Delete any existing unused reset codes for this user
        DB::table('wachtwoord_reset')
            ->where('gebruiker_id', $gebruiker->gebruiker_id)
            ->where('gebruikt', 0)
            ->delete();

        // Store the code as a hash so it cannot be read from the database
        DB::table('wachtwoord_reset')->insert([
            'gebruiker_id' => $gebruiker->gebruiker_id,
            'token' => Hash::make($code),
            'verlopen_op' => now()->addMinutes(10),
            'gebruikt' => 0,
        ]);

        // Always send to the registered email address from the database, never from user input
        $registeredEmail = $gebruiker->email;
        Mail::to($registeredEmail)->send(new ResetPasswordMail($code, $registeredEmail));

        // Store the gebruiker_id in session (NOT the email) to prevent tampering
        session([
            'reset_gebruiker_id' => $gebruiker->gebruiker_id,
            'reset_email' => $registeredEmail,
            'reset_attempts' => 0,
        ]);

        // Mask the email in the success message to prevent information leakage
        $maskedEmail = $this->maskEmail($registeredEmail);

        return redirect()->route('verify-code')
            ->with('success', 'De code is verstuurd naar ' . $maskedEmail . '.');
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

        // Get the gebruiker_id from session (not email, to prevent tampering)
        $gebruikerId = session('reset_gebruiker_id');
        $email = session('reset_email');

        if (!$gebruikerId || !$email) {
            return redirect()->route('recover-password')
                ->with('error', 'Sessie verlopen. Vraag een nieuwe code aan.');
        }

        // Check failed attempt count — max 5 attempts
        $attempts = session('reset_attempts', 0);
        if ($attempts >= 5) {
            // Invalidate all unused tokens for this user
            DB::table('wachtwoord_reset')
                ->where('gebruiker_id', $gebruikerId)
                ->where('gebruikt', 0)
                ->delete();

            // Clear session data
            session()->forget(['reset_gebruiker_id', 'reset_email', 'reset_attempts', 'reset_verified']);

            return redirect()->route('recover-password')
                ->with('error', 'Te veel mislukte pogingen. Vraag een nieuwe code aan.');
        }

        // Get the gebruiker record to verify it still exists
        $gebruiker = DB::table('gebruikers')->where('gebruiker_id', $gebruikerId)->first();

        if (!$gebruiker) {
            return redirect()->route('recover-password')
                ->with('error', 'Gebruiker niet gevonden.');
        }

        // Find all valid, unused, non-expired reset tokens for this user
        $resetRecords = DB::table('wachtwoord_reset')
            ->where('gebruiker_id', $gebruiker->gebruiker_id)
            ->where('gebruikt', 0)
            ->where('verlopen_op', '>', now())
            ->get();

        // Check the submitted code against hashed tokens
        $matchedRecord = null;
        foreach ($resetRecords as $record) {
            if (Hash::check($code, $record->token)) {
                $matchedRecord = $record;
                break;
            }
        }

        if (!$matchedRecord) {
            // Increment failed attempts
            session(['reset_attempts' => $attempts + 1]);

            $remainingAttempts = 5 - ($attempts + 1);
            $message = 'De code is onjuist of verlopen.';
            if ($remainingAttempts > 0) {
                $message .= ' Je hebt nog ' . $remainingAttempts . ' poging(en) over.';
            }

            return redirect()->route('verify-code')
                ->with('error', $message);
        }

        // Mark the token as used
        DB::table('wachtwoord_reset')
            ->where('reset_id', $matchedRecord->reset_id)
            ->update(['gebruikt' => 1]);

        // Store verification flag in session, bound to the specific gebruiker_id
        session([
            'reset_verified' => true,
            'reset_verified_gebruiker_id' => $gebruiker->gebruiker_id,
            'reset_attempts' => 0,
        ]);

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

        // Use the verified gebruiker_id from session — this cannot be tampered with
        $gebruikerId = session('reset_verified_gebruiker_id');

        if (!$gebruikerId) {
            return redirect()->route('recover-password')
                ->with('error', 'Sessie verlopen. Begin opnieuw.');
        }

        // Get the gebruiker record using the verified ID (not user-supplied email)
        $gebruiker = DB::table('gebruikers')->where('gebruiker_id', $gebruikerId)->first();

        if (!$gebruiker) {
            return redirect()->route('recover-password')
                ->with('error', 'Gebruiker niet gevonden. Begin opnieuw.');
        }

        // Update the password using gebruiker_id (not email) to prevent any tampering
        DB::table('gebruikers')
            ->where('gebruiker_id', $gebruikerId)
            ->update(['wachtwoord_hash' => Hash::make($request->password)]);

        if ($gebruiker) {
            \App\Models\Activiteit::log($gebruiker->gebruiker_id, 'wachtwoord_gewijzigd', [
                'email'   => $gebruiker->email,
                'details' => 'Wachtwoord succesvol hersteld via e-mail herstelcode verificatie.'
            ]);
        }

        // Clear all reset session data
        session()->forget([
            'reset_email',
            'reset_gebruiker_id',
            'reset_verified',
            'reset_verified_gebruiker_id',
            'reset_attempts',
        ]);

        return redirect()->route('login')
            ->with('success', 'Wachtwoord succesvol gewijzigd. Je kunt nu inloggen.');
    }

    /**
     * Mask an email address for display purposes.
     * e.g., "john.doe@example.com" becomes "j*****e@e****e.com"
     */
    private function maskEmail(string $email): string
    {
        $parts = explode('@', $email);
        $name = $parts[0];
        $domain = $parts[1];

        if (strlen($name) <= 2) {
            $maskedName = $name[0] . str_repeat('*', max(1, strlen($name) - 1));
        } else {
            $maskedName = $name[0] . str_repeat('*', strlen($name) - 2) . $name[strlen($name) - 1];
        }

        $domainParts = explode('.', $domain);
        $domainName = $domainParts[0];
        $domainExt = implode('.', array_slice($domainParts, 1));

        if (strlen($domainName) <= 2) {
            $maskedDomain = $domainName;
        } else {
            $maskedDomain = $domainName[0] . str_repeat('*', strlen($domainName) - 2) . $domainName[strlen($domainName) - 1];
        }

        return $maskedName . '@' . $maskedDomain . '.' . $domainExt;
    }
}