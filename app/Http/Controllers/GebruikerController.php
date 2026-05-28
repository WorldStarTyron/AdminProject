<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Gebruiker;

class GebruikerController extends Controller
{
    public function index(Request $request)
    {
        // search for gebruikers with rols
        $gebruikers = Gebruiker::with("rol")
        ->paginate(5);
         
        return view('GebruikersBeheerPagina', compact('gebruikers'));
    }   



    
}
