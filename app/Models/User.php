<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

// Standaard Laravel user, ik gebruik vooral Gebruiker
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'gebruikers';
    protected $primaryKey = 'gebruiker_id';
    public $incrementing = false;
    protected $keyType = 'string';

    const CREATED_AT = 'aangemaakt_op';
    const UPDATED_AT = 'bijgewerkt_op';

    protected $fillable = [
        'gebruiker_id',
        'naam',
        'email',
        'wachtwoord_hash',
        'rol',
    ];

    protected $hidden = [
        'wachtwoord_hash',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'wachtwoord_hash' => 'hashed',
    ];
}
