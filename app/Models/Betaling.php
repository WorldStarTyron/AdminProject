<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

// Betalingen van leden per maand
class Betaling extends Model
{
    use SoftDeletes;

    public $timestamps = false;

    protected $primaryKey = 'betaling_id';
    protected $table = 'betalingen';

    // Statussen die nog niet betaald zijn
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
        'volgende_deadline',
    ];

    protected $casts = [
        'volgende_deadline' => 'date',
    ];

    public function lid()
    {
        return $this->belongsTo(Lid::class, 'lid_id');
    }

    public function bon()
    {
        return $this->hasOne(Bonnen::class, 'betaling_id', 'betaling_id');
    }

    public function scopeVoorMaand($query, int $maand, int $jaar)
    {
        return $query->where('maand', $maand)->where('jaar', $jaar);
    }

    public static function isBetaald(?string $status): bool
    {
        return strtolower((string) $status) === 'betaald';
    }

    public static function isOnbetaald(?string $status): bool
    {
        return in_array($status, self::ONBETAALDE_STATUSSEN, true);
    }

    // Berekent de volgende deadline (basisdatum + 1 maand)
    public function berekenVolgendeDeadline(?string $betaaldOp = null): Carbon
    {
        $basisDatum = Carbon::parse($betaaldOp ?? $this->ingediend_op);
        // addMonthNoOverflow: 31 jan + 1 maand wordt 28 feb, geen 3 maart
        $this->volgende_deadline = $basisDatum->copy()->addMonthNoOverflow();
        $this->save();

        return $this->volgende_deadline;
    }

    // Totaal van alle betaalde betalingen
    public static function totaleInkomsten(): float
    {
        return (float) self::whereIn('status', ['betaald', 'goed_gekeurd'])->sum('bedrag');
    }

    public static function openstaandBedrag(int $maand, int $jaar): float
    {
        return (float) self::voorMaand($maand, $jaar)
            ->whereIn('status', self::ONBETAALDE_STATUSSEN)
            ->sum('bedrag');
    }

    // Aantal actieve leden die deze maand betaald hebben
    public static function aantalBetaaldeLeden(int $maand, int $jaar): int
    {
        return (int) Lid::metActieveGebruiker()
            ->whereHas('betalingen', function ($q) use ($maand, $jaar) {
                $q->voorMaand($maand, $jaar)
                  ->whereIn('status', ['betaald', 'goed_gekeurd']);
            })
            ->count();
    }

    // Alle dashboard cijfers in 1 functie
    public static function dashboardStatistieken(int $maand, int $jaar): array
    {
        $totaalLeden = Lid::totaalActieveLeden();
        $totaalBetaald = self::aantalBetaaldeLeden($maand, $jaar);

        // max(0,...) voor het geval er iets misgaat in de telling
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
