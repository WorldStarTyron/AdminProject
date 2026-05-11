<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lid extends Model
{
    use HasFactory;

    protected $table = "leden"; // Members table in database
    protected $primaryKey = "lid_id"; // Primary key in database
    public $incrementing = false;
    protected $keyType = 'string';

    public $timestamps = true; // Track creation and update times
    const CREATED_AT = 'lid_sinds';
    const UPDATED_AT = 'bijgewerkt_op';

    /**
     * Fillable fields - these match the database table structure
     * Form submits (lowercase): name, email, telefoonnummer, adres, woonplaats, geboortedatum
     * Database expects (with capitals): Naam, Email
     * The PostController transforms the data before saving
     */
    protected $fillable = [
        "lid_id",
        "gebruiker_id",
        "naam",
        "email",
        "telefoonnummer",
        "adres",
        "woonplaats",
        "geboortedatum",
    ];
}

