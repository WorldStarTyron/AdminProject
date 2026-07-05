<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use App\Mail\ResetPasswordMail;

// Wachtwoord vergeten flow (3 stappen)
class PasswordResetController extends Controller
{
    public function show()
    {
        return view('ForgetPassword.Recover-password');
    }

    // Stap 1: stuur reset code
    public function sendResetCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:gebruikers,email'
        ]);

        $email = $request->email;

        // Ingelogde gebruikers mogen alleen voor zichzelf een code aanvragen
        if (auth()->check() && strtolower($email) !== strtolower(auth()->user()->email)) {
            return back()
                ->withInput()
                ->with('error', 'U kunt alleen een verificatiecode sturen naar uw eigen e-mailadres.');
        }

        // Max 3 aanvragen per 10 min
        $rateLimitKey = 'password-reset:' . strtolower($email);
        if (RateLimiter::tooManyAttempts($rateLimitKey, 3)) {
            $seconds = RateLimiter::availableIn($rateLimitKey);
            return back()
                ->withInput()
                ->with('error', 'Te veel verification codes verstuurd. Probeer het opnieuw over ' . ceil($seconds / 60) . ' minuten.');
        }
        RateLimiter::hit($rateLimitKey, 900);

        $gebruiker = DB::table('gebruikers')->where('email', $email)->first();

        // 6 cijferige random code
        $code = str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT);

        // Oude codes weggooien
        DB::table('wachtwoord_reset')
            ->where('gebruiker_id', $gebruiker->gebruiker_id)
            ->where('gebruikt', 0)
            ->delete();

        // Code gehasht opslaan
        DB::table('wachtwoord_reset')->insert([
            'gebruiker_id' => $gebruiker->gebruiker_id,
            'token' => Hash::make($code),
            'verlopen_op' => now()->addMinutes(10),
            'gebruikt' => 0,
        ]);

        // Mailen naar adres uit db, niet uit input
        $registeredEmail = $gebruiker->email;
        Mail::to($registeredEmail)->send(new ResetPasswordMail($code, $registeredEmail));

        // ID in sessie (niet email) zodat het niet te wijzigen is
        session([
            'reset_gebruiker_id' => $gebruiker->gebruiker_id,
            'reset_email' => $registeredEmail,
            'reset_attempts' => 0,
        ]);

        $maskedEmail = $this->maskEmail($registeredEmail);

        return redirect()->route('verify-code')
            ->with('success', 'De code is verstuurd naar ' . $maskedEmail . '.');
    }

    public function verifyCode()
    {
        return view('ForgetPassword.Verifycode');
    }

    // Stap 2: code controleren
    public function postVerifyCode(Request $request)
    {
        $request->validate([
            'code' => 'required|numeric|digits:6',
        ]);

       $code = $request->code;

        $gebruikerId = session('reset_gebruiker_id');
        $email = session('reset_email');

        if (!$gebruikerId || !$email) {
            return redirect()->route('recover-password')
                ->with('error', 'Sessie verlopen. Vraag een nieuwe code aan.');
        }

        // Max 5 pogingen
        $attempts = session('reset_attempts', 0);
        if ($attempts >= 5) {
            DB::table('wachtwoord_reset')
                ->where('gebruiker_id', $gebruikerId)
                ->where('gebruikt', 0)
                ->delete();

            session()->forget(['reset_gebruiker_id', 'reset_email', 'reset_attempts', 'reset_verified']);

            return redirect()->route('recover-password')
                ->with('error', 'Te veel onjuiste verificatie codes ingevoerd. Vraag een nieuwe code aan.');
        }

        $gebruiker = DB::table('gebruikers')->where('gebruiker_id', $gebruikerId)->first();

        if (!$gebruiker) {
            return redirect()->route('recover-password')
                ->with('error', 'Gebruiker niet gevonden.');
        }

        // Alle geldige codes ophalen
        $resetRecords = DB::table('wachtwoord_reset')
            ->where('gebruiker_id', $gebruiker->gebruiker_id)
            ->where('gebruikt', 0)
            ->where('verlopen_op', '>', now())
            ->get();

        // Vergelijken met Hash::check
        $matchedRecord = null;
        foreach ($resetRecords as $record) {
            if (Hash::check($code, $record->token)) {
                $matchedRecord = $record;
                break;
            }
        }

        if (!$matchedRecord) {
            session(['reset_attempts' => $attempts + 1]);

            $remainingAttempts = 5 - ($attempts + 1);
            $message = 'De code is onjuist of verlopen.';
            if ($remainingAttempts > 0) {
                $message .= ' Je hebt nog ' . $remainingAttempts . ' om de code correct in te voeren.';
            }

            return redirect()->route('verify-code')
                ->with('error', $message);
        }

        // Code als gebruikt markeren
        DB::table('wachtwoord_reset')
            ->where('reset_id', $matchedRecord->reset_id)
            ->update(['gebruikt' => 1]);

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

    // Stap 3: nieuw wachtwoord opslaan
    public function postNewPassword(Request $request)
    {
        $request->validate([
            'password' => 'required|min:8|confirmed',
        ]);

        if (!session('reset_verified')) {
            return redirect()->route('recover-password')
                ->with('error', 'Verificatie niet voltooid. Begin opnieuw.');
        }

        // ID uit sessie gebruiken, niet uit form
        $gebruikerId = session('reset_verified_gebruiker_id');

        if (!$gebruikerId) {
            return redirect()->route('recover-password')
                ->with('error', 'Sessie verlopen. Begin opnieuw.');
        }

        $gebruiker = DB::table('gebruikers')->where('gebruiker_id', $gebruikerId)->first();

        if (!$gebruiker) {
            return redirect()->route('recover-password')
                ->with('error', 'Gebruiker niet gevonden. Begin opnieuw.');
        }

        DB::table('gebruikers')
            ->where('gebruiker_id', $gebruikerId)
            ->update(['wachtwoord_hash' => Hash::make($request->password)]);

        if ($gebruiker) {
            \App\Models\Activiteit::log($gebruiker->gebruiker_id, 'wachtwoord_gewijzigd', [
                'email'   => $gebruiker->email,
                'details' => 'Wachtwoord succesvol hersteld via e-mail herstelcode verificatie.'
            ]);
        }

        // Sessie helemaal opschonen
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

    // Email maskeren bv. j*****e@e*****e.com
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
