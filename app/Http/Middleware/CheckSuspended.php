<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\Auth;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSuspended
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Alleen automatisch geschorste leden (suspension_at gevuld) blokkeren;
        // handmatig gedeactiveerde accounts vallen hier niet onder
        if (auth()->check() && auth()->user()->isSuspended()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->withErrors(['email' => 'Uw account is niet meer actief wegens betalingsachterstand.']);
        }

        return $next($request);
    }
}
