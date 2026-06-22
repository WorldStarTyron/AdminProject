<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Meldingen voor admins
class Notificatie extends Model
{
    use HasFactory;

    protected $table = 'notificatie';
    protected $primaryKey = 'notificatie_id';

    public $timestamps = false;

    protected $fillable = [
        'gebruiker_id',
        'lid_id',
        'Notif_type',
        'titel',
        'gelezen',
        'gestuurd_op',
    ];

    protected $casts = [
        // 0/1 in db wordt true/false in php
        'gelezen' => 'boolean',
        'gestuurd_op' => 'datetime',
    ];

    public function gebruiker()
    {
        return $this->belongsTo(Gebruiker::class, 'gebruiker_id', 'gebruiker_id');
    }

    public function lid()
    {
        return $this->belongsTo(Lid::class, 'lid_id', 'lid_id');
    }
}
