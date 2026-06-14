<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bonnen;
use App\Models\Betaling;

class BonController extends Controller
{
    
    // Standaard Bon Genereer
  public static function genereer(Betaling $betaling, string $gebruikerNaam, string $datum) {
    // Alleen bon aanmaken als status 'betaald' is
    if ($betaling->status !== 'betaald') {
        return null;
    }

    $year = now()->year;
    $aantalDitJaar = Bonnen::whereYear('aangemaakt_op', $year)->count();

    // Loop until we find a unique receipt number
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

    public function store(Request $request) {
        //Store Bonnen
    }

    public function edit(Request $request) {
        //Edit Bonnen
    }

    public function update(Request $request) {
        //Update Bonnen
    }

    public function destroy(Request $request) {
        //Delete Bonnen
    }

    public function download(Request $request) {
        //Download Bonnen
    }
}
