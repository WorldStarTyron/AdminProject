<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Hash;

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

    // Relationship: Gebruiker -> Lid
    public function lid()
    {
        return $this->hasOne(Lid::class, 'gebruiker_id', 'gebruiker_id');
    }

    // Relationship: Many-to-many with Rol
    public function rollen()
    {
        return $this->belongsToMany(Rol::class, 'gebruikers_rollen', 'gebruiker_id', 'rol_id')
            ->withPivot('toegewezen_op');
    }

    // Singular belongsTo (if still needed)
    public function rol()
    {
        return $this->belongsTo(Rol::class, 'rol_id', 'rol_id');
    }

    // Relationship: A user has many notifications
    public function notificaties()
    {
        return $this->hasMany(Notificatie::class, 'gebruiker_id', 'gebruiker_id');
    }

    // Check if user has any of the given role names
    public function hasAnyRole($roles): bool
    {
        $roles = is_array($roles) ? $roles : [$roles];
        
        // Normalize role names to also check "Applicatie beheerder" if "Applicatie Beheerder" is passed, and vice versa
        $normalizedRoles = [];
        foreach ($roles as $role) {
            $normalizedRoles[] = $role;
            if ($role === 'Applicatie Beheerder') {
                $normalizedRoles[] = 'Applicatie beheerder';
            } elseif ($role === 'Applicatie beheerder') {
                $normalizedRoles[] = 'Applicatie Beheerder';
            }
        }
        
        return $this->rollen()->whereIn('naam', $normalizedRoles)->exists();
    }

    // Check if user has the 'Lid' role
    public function isLid(): bool
    {
        return $this->rollen()->where('naam', 'Lid')->exists();
    }

    // Check if user has the applicatiebeheerder role
    public function isApplicatieBeheerder(): bool
    {
        return $this->rollen()->whereIn('naam', ['Applicatie beheerder', 'ApplicatieBeheerder', 'Applicatie Beheerder'])->exists();
    }

    // Check if user has the voorzitter role
    public function isVoorzitter(): bool
    {
        return $this->rollen()->where('naam', 'Voorzitter')->exists();
    }

    // Hash and set password
    public function setPasswordAttribute($plainPassword)
    {
        $this->attributes['wachtwoord_hash'] = Hash::make($plainPassword);
    }

    // Verify plain password against stored hash
    public function verifyPassword($plainPassword): bool
    {
        return Hash::check($plainPassword, $this->wachtwoord_hash);
    }

    // Tell Laravel which column is the password column
    public function getAuthPassword()
    {
        return $this->wachtwoord_hash;
    }
}