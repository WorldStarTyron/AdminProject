<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Betaling extends Model
{
    use SoftDeletes;

    public $timestamps = false;

    protected $primaryKey = 'betaling_id';

    // ← Tell Laravel the correct table name
    protected $table = 'betalingen';

    // Statussen die als onbetaald/open tellen op het dashboard
    public const ONBETAALDE_STATUSSEN = ['niet_betaald', 'Openstaand', 'in_afwachting'];

    protected $fillable = [
        'lid_id',
        'bedrag',
        'methode',
        'status',
        'maand',
        'jaar',
        'betaling_bewijs',
        'ingediend_op',
    ];

    public function lid()
    {
        return $this->belongsTo(Lid::class, 'lid_id');
    }

    public function bon()
    {
        return $this->hasOne(Bonnen::class, 'betaling_id', 'betaling_id');
    }

    // Filter betalingen op een specifieke maand en jaar
    public function scopeVoorMaand($query, int $maand, int $jaar)
    {
        return $query->where('maand', $maand)->where('jaar', $jaar);
    }

    // Controleer of een status als betaald telt
    public static function isBetaald(?string $status): bool
    {
        return strtolower((string) $status) === 'betaald';
    }

    // Controleer of een status als onbetaald/open telt
    public static function isOnbetaald(?string $status): bool
    {
        return in_array($status, self::ONBETAALDE_STATUSSEN, true);
    }

// Som van alle goedgekeurde/betaalde betalingen
    // Includes both 'betaald' and 'goed_gekeurd' as paid income
    public static function totaleInkomsten(): float
    {
        return (float) self::whereIn('status', ['betaald', 'goed_gekeurd'])->sum('bedrag');
    }

    // Som van openstaande bedragen voor een maand (niet_betaald + Openstaand + in_afwachting)
    public static function openstaandBedrag(int $maand, int $jaar): float
    {
        return (float) self::voorMaand($maand, $jaar)
            ->whereIn('status', self::ONBETAALDE_STATUSSEN)
            ->sum('bedrag');
    }

// Aantal actieve leden met een betaalde betaling in de gekozen maand
    // Includes both 'betaald' and 'goed_gekeurd'
    public static function aantalBetaaldeLeden(int $maand, int $jaar): int
    {
        return (int) Lid::metActieveGebruiker()
            ->whereHas('betalingen', function ($q) use ($maand, $jaar) {
                $q->voorMaand($maand, $jaar)
                  ->whereIn('status', ['betaald', 'goed_gekeurd']);
            })
            ->count();
    }

    // Alle dashboard statistieken op één plek berekenen
    public static function dashboardStatistieken(int $maand, int $jaar): array
    {
        $totaalLeden = Lid::totaalActieveLeden();
        $totaalBetaald = self::aantalBetaaldeLeden($maand, $jaar);

        // Onbetaald = actieve leden minus betaalde leden (zelfde logica als de tabel)
        $totaalNietBetaald = max(0, $totaalLeden - $totaalBetaald);

        return [
            'totaleInkomsten'   => self::totaleInkomsten(),
            'openstaandBedrag'  => self::openstaandBedrag($maand, $jaar),
            'totaalLeden'       => $totaalLeden,
            'totaalBetaald'     => $totaalBetaald,
            'totaalNietBetaald' => $totaalNietBetaald,
        ];
    }
}
