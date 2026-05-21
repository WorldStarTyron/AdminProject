<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lid extends Model
{
    use HasFactory;

    protected $table = "leden"; // Members table in database
    protected $primaryKey = "lid_id"; // Primary key in database
    public $incrementing = true; // True for auto-incrementing primary key
    // protected $keyType = 'string'; // Primary key type

    public $timestamps = true; // Track creation and update times
    const CREATED_AT = 'lid_sinds';
    const UPDATED_AT = 'bijgewerkt_op';

    /**
     * Fillable fields - these match the database table structure
     * Form submits (lowercase): lid_id, gebruiker_id, telefoonnummer, adres, woonplaats, geboortedatum
     * Database expects (with capitals): lid_id, gebruiker_id, telefoonnummer, adres, woonplaats, geboortedatum
     * The PostController transforms the data before saving
     */
    protected $fillable = [
        "lid_id",
        "gebruiker_id",
        "telefoonnummer",
        "adres",
        "lid_type",
        "woonplaats",
        "geboortedatum",
    ];
}

