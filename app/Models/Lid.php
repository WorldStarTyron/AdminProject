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

    // Alleen leden met een actieve gebruiker (inactieve gebruikers tellen niet mee)
    public function scopeMetActieveGebruiker($query)
    {
        return $query->whereHas('gebruiker', function ($q) {
            $q->where('status', 'Actief');
        });
    }

    // Totaal aantal actieve leden voor het dashboard
    public static function totaalActieveLeden(): int
    {
        return (int) self::metActieveGebruiker()->count();
    }

    // Haal de betaling van de huidige maand op voor dit lid
    public function betalingVoorMaand(int $maand, int $jaar)
    {
        return $this->betalingen
            ->where('maand', $maand)
            ->where('jaar', $jaar)
            ->first();
    }

/**
     * Relatie: de meest recente betaalde betaling voor dit lid.
     * Wordt gebruikt om de volgende_deadline te bepalen.
     *
     * IMPORTANT: Order by ingediend_op (submission date), NOT betaling_id.
     * A higher betaling_id doesn't mean a more recent payment!
     */
    public function laatsteBetaaldeBetaling()
    {
        return $this->hasOne(Betaling::class, 'lid_id', 'lid_id')
            ->whereIn('status', ['betaald', 'goed_gekeurd'])
            ->orderBy('ingediend_op', 'desc');
    }

    /**
     * Geeft de actieve (volgende) deadline voor dit lid.
     * - Als er een betaalde betaling is met volgende_deadline → gebruik die
     * - Als het lid nog nooit betaald heeft → lid_sinds + 1 maand
     *
     * @return \Carbon\Carbon|null
     */
    public function actieveDeadline(): ?\Carbon\Carbon
    {
        $laatsteBetaling = $this->laatsteBetaaldeBetaling;

        if ($laatsteBetaling && $laatsteBetaling->volgende_deadline) {
            return \Carbon\Carbon::parse($laatsteBetaling->volgende_deadline);
        }
  
        
        // Fallback: begin van huidige maand (niet lid_sinds, want dat kan jaren geleden zijn)
        return \Carbon\Carbon::now()->startOfMonth();
    }

}

