<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gebruiker extends Model
{
    use HasFactory;
    protected $table = "gebruikers";
    protected $primaryKey = "gebruiker_id";
    public $incrementing = true;
    public $timestamps = true;
    const CREATED_AT = 'aangemaakt_op';
    const UPDATED_AT = 'bijgewerkt_op';

    protected $fillable = [
        "gebruiker_id",
        "naam",
        "email",
        "wachtwoord_hash",
        "actief",
    ];

    protected $hidden = [
        "wachtwoord_hash",
    ];
    //this is to define the relationship between the gebruiker and lid model
    public function lid()
    {
        return $this->hasOne(Lid::class, 'gebruiker_id', 'gebruiker_id');
    }
}