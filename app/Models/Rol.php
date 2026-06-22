<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// De rollen in het systeem
class Rol extends Model
{
    public $timestamps = false;
    protected $primaryKey = 'rol_id';
    protected $table = 'rollen';

    protected $fillable = [
        'naam',
        'omschrijving'
    ];

    public function gebruikers()
    {
        return $this->belongsToMany(Gebruiker::class, 'gebruikers_rollen', 'rol_id', 'gebruiker_id');
    }
}
