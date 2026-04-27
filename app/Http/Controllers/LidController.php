<?php

namespace App\Http\Controllers;
use App\Models\Lid;
use Illuminate\Http\Request;

class LidController extends Controller
{
    public function index()
    {
        $leden = Lid::select(
            'leden.lid_id',
            'gebruikers.naam',
            'leden.telefoonnummer',
            'leden.adres',
            'gebruikers.email'
        )->join('gebruikers','leden.gebruiker_id','=','gebruikers.gebruiker_id')
        ->get();

        $totaalLeden = Lid::count();

        return view('ledenpagina', compact('leden', 'totaalLeden'));
    }
}
