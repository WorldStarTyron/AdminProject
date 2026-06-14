<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notificatie extends Model
{
    use HasFactory;

    protected $table = 'notificatie';
    protected $primaryKey = 'notificatie_id';
    
    // There are no standard created_at/updated_at columns, only gestuurd_op
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
        'gelezen' => 'boolean',
        'gestuurd_op' => 'datetime',
    ];

    // A notification belongs to a user
    public function gebruiker()
    {
        return $this->belongsTo(Gebruiker::class, 'gebruiker_id', 'gebruiker_id');
    }

    // A notification belongs to a member
    public function lid()
    {
        return $this->belongsTo(Lid::class, 'lid_id', 'lid_id');
    }
}
