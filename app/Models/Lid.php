<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lid extends Model
{
    use HasFactory;

    protected $table = "leden"; // Ledentabel in database
    protected $primaryKey = "lid_id"; // Primary key in database
    public $incrementing = false;
    protected $keyType = 'string';

    public $timestamps = true; // Timestamps aanzetten overzicht wanneer id gemaakt en aangepast is

    const CREATED_AT = 'lid_sinds';
    const UPDATED_AT = 'bijgewerkt_op';

    protected $fillable = [
        "lid_id",
        "gebruiker_id",
        "telefoonnummer",
        "adres",
        "woonplaats",
        "geboortedatum",
        "betaalstatus"
    ];

    

}
