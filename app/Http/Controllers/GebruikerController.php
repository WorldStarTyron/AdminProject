<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Gebruiker;
use App\Models\Rol;

class GebruikerController extends Controller
{
    public function index(Request $request)
    {
        //Searchbare
        $zoek = $request->input('zoek');

        //Filters
        $status = $request->input('status');
        $rol = $request->input('rol');

        $gebruikers = Gebruiker::with('rollen')
        ->when($zoek, function($query) use ($zoek){
            $query->where('naam', 'like', "%{$zoek}%")
                  ->orWhere('email', 'like', "%{$zoek}%");
                  
        })
        ->when($status, function($query) use ($status){
            $query->where('status', $status);
        })
        ->when($rol, function($query) use ($rol){
            $query->whereHas('rollen', function($q) use ($rol){
                $q->where('naam', $rol);
            });
        })
        ->paginate(5)
        ->appends($request->query());

        
         // Count how many Admins, Voorzitters & gebruikers
        $totaalGebruikers = Gebruiker::count();
        $totaalAdmins = Gebruiker::whereHas('rollen', fn($q) => $q->where('naam', 'Administratie Medewerker'))->count();
        $totaalVoorzitters = Gebruiker::whereHas('rollen', fn($q) => $q->where('naam', 'Voorzitter'))->count();

        // Get all roles for filter dropdown
        $rollen = Rol::all();

        return view('GebruikersBeheerPagina', compact(
            'gebruikers', 'totaalGebruikers', 'totaalAdmins', 'totaalVoorzitters', 'rollen'));
    }   



    


    
}
