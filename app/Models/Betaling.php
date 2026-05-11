<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Betaling extends Model
{
    use HasFactory;

    protected $table = 'betalingen';
    protected $primaryKey = 'betaling_id';
    public $incrementing = false;
    protected $keyType = 'string';

    public $timestamps = false; // Using custom timestamps in migration

    protected $fillable = [
        'betaling_id',
        'lid_id',
        'bedrag',
        'methode',
        'status',
        'betalingsdatum',
        'maand',
        'jaar',
        'bewijs_bestand',
    ];

    public function lid()
    {
        return $this->belongsTo(Lid::class, 'lid_id', 'lid_id');
    }
}
