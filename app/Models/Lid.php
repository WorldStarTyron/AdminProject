<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Lid van de vereniging, gekoppeld aan een gebruiker
class Lid extends Model
{
    use HasFactory;

    protected $table = "leden";
    protected $primaryKey = "lid_id";
    public $incrementing = true;

    public $timestamps = true;
    const CREATED_AT = 'lid_sinds';
    const UPDATED_AT = 'bijgewerkt_op';

    public function getCreatedAtColumn()
    {
        return static::CREATED_AT;
    }

    public function getUpdatedAtColumn()
    {
        return static::UPDATED_AT;
    }

    protected $casts = [
        'lid_sinds' => 'datetime',
        'bijgewerkt_op' => 'datetime',
    ];

    // Naam en email staan in gebruikers, niet hier
    protected $fillable = [
        "gebruiker_id",
        "telefoonnummer",
        "adres",
        "lid_type",
        "woonplaats",
        "geboortedatum",
        "lid_sinds",
    ];

    public function betalingen()
    {
        return $this->hasMany(Betaling::class, 'lid_id', 'lid_id');
    }

    public function gebruiker()
    {
        return $this->belongsTo(Gebruiker::class, 'gebruiker_id', 'gebruiker_id');
    }

    public function notificaties()
    {
        return $this->hasMany(Notificatie::class, 'lid_id', 'lid_id');
    }

    // Vaste bijdrage per maand
    public function MaandelijkseBijdrage(): float
    {
        return 150.00;
    }

    // Alleen leden waarvan de gebruiker actief is
    public function scopeMetActieveGebruiker($query)
    {
        return $query->whereHas('gebruiker', function ($q) {
            $q->where('status', 'Actief');
        });
    }

    public static function totaalActieveLeden(): int
    {
        return (int) self::metActieveGebruiker()->count();
    }

    public function betalingVoorMaand(int $maand, int $jaar)
    {
        return $this->betalingen
            ->where('maand', $maand)
            ->where('jaar', $jaar)
            ->first();
    }

    // Meest recente betaalde betaling
    // Sorteer op ingediend_op, niet op id
    public function laatsteBetaaldeBetaling()
    {
        return $this->hasOne(Betaling::class, 'lid_id', 'lid_id')
            ->whereIn('status', ['betaald', 'goed_gekeurd'])
            ->orderBy('ingediend_op', 'desc');
    }

    // Volgende deadline voor dit lid
    public function actieveDeadline(): ?\Carbon\Carbon
    {
        $laatsteBetaling = $this->laatsteBetaaldeBetaling;

        // Pak volgende_deadline uit de laatste betaalde betaling (ingediend_op + 1 maand)
        if ($laatsteBetaling && $laatsteBetaling->volgende_deadline) {
            return \Carbon\Carbon::parse($laatsteBetaling->volgende_deadline);
        }

        // Nog nooit betaald, neem einde huidige maand als deadline
        return \Carbon\Carbon::now()->endOfMonth();
    }
}
