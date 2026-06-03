<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Betaling extends Model
{
    public $timestamps = false;

    protected $primaryKey = 'betaling_id';

    // ← Tell Laravel the correct table name
    protected $table = 'betalingen';

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
}