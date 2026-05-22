<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Betaling;
use App\Models\Lid;
use Illuminate\Support\Str;
use Carbon\Carbon;

class BetalingController extends Controller
{
    public function index()
    {
        $betalingen = Betaling::orderBy('betalingsdatum', 'desc')->paginate(10);
        return view('BetalingPagina', compact('betalingen'));
    }

    public function store(Request $request)
    {
        // Basic validation
        $request->validate([
            'naam'             => 'required|string',
            'datum'            => 'required|date',
            'methode'          => 'required|in:Geld,Overmaking',
            'status'           => 'required|in:in_behandeling,betaald,niet_betaald',
            'bedrag'           => 'required|numeric|min:0',
            'lid_type'         => 'required|in:Actief,Passief,Bijzonder',
            'betaling_bewijs'  => 'nullable|file|max:5120',
        ]);

        // Check if the lid exists by naam
        $lid = Lid::where('naam', $request->naam)->first();

        if (!$lid) {
            return response()->json([
                'errors' => [
                    'naam' => ['Dit lid bestaat niet. Controleer de naam en probeer opnieuw.']
                ]
            ], 422);
        }

        // Handle file upload
        $bewijsPath = null;
        if ($request->hasFile('betaling_bewijs')) {
            $bewijsPath = $request->file('betaling_bewijs')->store('bewijzen', 'public');
        }

        // Auto-derive maand and jaar from datum
        $datum = Carbon::parse($request->datum);

        Betaling::create([
            'betaling_id'     => (string) Str::uuid(),
            'lid_id'          => $lid->lid_id,
            'lid_type'        => $request->lid_type,
            'bedrag'          => $request->bedrag, 
            'methode'         => $request->methode,
            'status'          => $request->status,
            'maand'           => $datum->month,
            'jaar'            => $datum->year,
            'betaling_bewijs'  => $bewijsPath,
        ]);

        return response()->json(['success' => true, 'message' => 'Betaling succesvol toegevoegd'], 201);
    }
}
