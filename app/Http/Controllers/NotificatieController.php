<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notificatie;
use Illuminate\Support\Facades\Auth;

class NotificatieController extends Controller
{
    public function index(){
        if (!Auth::user()->hasAnyRole(['Administratie Medewerker', 'Applicatie Beheerder'])) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        //Haalt de notifcaties op van de ingelogde gebruiker
        
        $notificatie = Notificatie::where('gebruiker_id', Auth::id())
        ->orderBy('gestuurd_op','desc')
        ->take(10)
        ->get(); 

        $ongelezen = Notificatie::where('gebruiker_id', Auth::id())
            ->where('gelezen', false)
            ->count();

         return response()->json([
            'notificaties' => $notificatie,
            'ongelezen' => $ongelezen
         ]);    
    }



    
    // Markeert alle notifcaties van de ingelogde gebruiker als gelezen
    public function markeerGelezen(){
        if (!Auth::user()->hasAnyRole(['Administratie Medewerker', 'Applicatie Beheerder'])) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $notificatie = Notificatie::where('gebruiker_id', Auth::id())
        ->where('gelezen',false)
        ->update(['gelezen' => true]);

        return response()->json(['success' => true]);    
    }
}
