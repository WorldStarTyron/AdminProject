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
     * Ensure Eloquent uses the correct timestamp column names.
     * This prevents code from accidentally using `created_at`/`updated_at`.
     */
    public function getCreatedAtColumn()
    {
        return static::CREATED_AT;
    }

    public function getUpdatedAtColumn()
    {
        return static::UPDATED_AT;
    }

    /**
     * Cast timestamp columns to Carbon instances for easy use.
     */
    protected $casts = [
        'lid_sinds' => 'datetime',
        'bijgewerkt_op' => 'datetime',
    ];

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
        "lid_sinds",
    ];

    // Een lid heeft veel betalingen
    public function betalingen()
    {
        return $this->hasMany(Betaling::class, 'lid_id', 'lid_id');
    }

    // Een lid hoort bij een gebruiker
    public function gebruiker()
    {
        return $this->belongsTo(Gebruiker::class, 'gebruiker_id', 'gebruiker_id');
    }

    // Een lid heeft veel notificaties
    public function notificaties()
    {
        return $this->hasMany(Notificatie::class, 'lid_id', 'lid_id');
    }


    //elk lid type betaalt SRD 100 per maand (behalve bijzonder lidmaatschap)
    public function MaandelijkseBijdrage(): float
{
    return 150.00; 
}

}

 