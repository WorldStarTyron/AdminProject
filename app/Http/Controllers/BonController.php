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

    $aantalDitJaar = Bonnen::whereYear('aangemaakt_op', now()->year)->count();
    $bonNummer = 'BON-KA' . now()->year . '-' . str_pad($aantalDitJaar + 1, 4, '0', STR_PAD_LEFT);

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
