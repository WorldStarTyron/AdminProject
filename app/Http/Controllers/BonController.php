<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bonnen;
use App\Models\Betaling;

// Bonnen (kwitanties) generator
class BonController extends Controller
{
    // Maakt automatisch een bon aan na een betaling
    public static function genereer(Betaling $betaling, string $gebruikerNaam, string $datum)
    {
        // Alleen voor betaalde betalingen
        if ($betaling->status !== 'betaald') {
            return null;
        }

        $year = now()->year;
        $aantalDitJaar = Bonnen::whereYear('aangemaakt_op', $year)->count();

        // Zoek het volgende vrije bonnummer
        $index = $aantalDitJaar + 1;
        do {
            $bonNummer = 'BON-KA' . $year . '-' . str_pad($index, 4, '0', STR_PAD_LEFT);
            $index++;
        } while (Bonnen::where('bon_nummer', $bonNummer)->exists());

        return Bonnen::create([
            'betaling_id'   => $betaling->betaling_id,
            'bon_nummer'    => $bonNummer,
            'beschrijving'  => 'Contributie van ' . $gebruikerNaam . ' voor ' . $datum,
            'aangemaakt_op' => now(),
        ]);
    }

    public function store(Request $request) {}
    public function edit(Request $request) {}
    public function update(Request $request) {}
    public function destroy(Request $request) {}
    public function download(Request $request) {}
}
