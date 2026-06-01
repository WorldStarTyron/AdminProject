<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activiteit extends Model
{
    use HasFactory;

    protected $table = "activiteit";
    protected $primaryKey = "log_id";
    public $timestamps = false;

    protected $fillable = [
        'gebruiker_id',
        'actie',
        'details',
        'aangemaakt_op',
    ];

    protected $casts = [
        'details' => 'array',
        'aangemaakt_op' => 'datetime',
    ];

    /**
     * Unified system-wide helper to cleanly log user or administrator actions.
     *
     * @param int $gebruikerId The ID of the user performing the action
     * @param string $actie One of the predefined database enum actions
     * @param array|null $details Structured contextual metadata surrounding the event
     * @param string|null $timestamp Optional historical timestamp for seeders
     * @return self
     */
    public static function log(int $gebruikerId, string $actie, ?array $details = null, ?string $timestamp = null)
    {
        return self::create([
            'gebruiker_id'  => $gebruikerId,
            'actie'         => $actie,
            'details'       => $details,
            'aangemaakt_op' => $timestamp ?? now(),
        ]);
    }

    // Een activiteit hoort bij een gebruiker
    public function gebruiker()
    {
        return $this->belongsTo(Gebruiker::class, 'gebruiker_id', 'gebruiker_id');
    }
}
