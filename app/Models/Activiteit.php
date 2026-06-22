<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Houdt alle acties van gebruikers bij
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
        // details staat als JSON in de db
        'details' => 'array',
        'aangemaakt_op' => 'datetime',
    ];

    // Hulpfunctie om vanuit elke controller een log aan te maken
    public static function log(int $gebruikerId, string $actie, ?array $details = null, ?string $timestamp = null)
    {
        return self::create([
            'gebruiker_id'  => $gebruikerId,
            'actie'         => $actie,
            'details'       => $details,
            'aangemaakt_op' => $timestamp ?? now(),
        ]);
    }

    public function gebruiker()
    {
        return $this->belongsTo(Gebruiker::class, 'gebruiker_id', 'gebruiker_id');
    }
}
