<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Gebruiker extends Authenticatable
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
        "status",
    ];

    protected $hidden = [
        "wachtwoord_hash",
    ];

    protected $casts = [
        'aangemaakt_op' => 'datetime',
        'bijgewerkt_op' => 'datetime',
    ];

    //this is to define the relationship between the gebruiker and lid model 
    public function lid()
    {
        return $this->hasOne(Lid::class, 'gebruiker_id', 'gebruiker_id');
    }


//Many to many Relation with Rol
public function rollen()
{
    return $this->belongsToMany(Rol::class, 'gebruikers_rollen', 'gebruiker_id', 'rol_id')
        ->withPivot('toegewezen_op'); // alleen wat echt bestaat
}

//Set password and hash it
public function setPasswordAttribute($plainPassword)
{
    $this->attributes['wachtwoord_hash'] = Hash::make($plainPassword);
}

//check password
  public function verifyPassword($plainPassword)
    {
        return Hash::check($plainPassword, $this->wachtwoord_hash);
    }

    //vertel Laravel dat dit de wachtwoord kolom is.
    public function getAuthPassword()
    {
        return $this->wachtwoord_hash;
    }
   
   // this function is to define the relationship between the gebruiker and rol model 
    public function rol()
    {
        return $this->belongsTo(Rol::class, 'rol_id');
    }
  
    
}