<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Hash;

// Gebruikersmodel voor login
// Erft van Authenticatable zodat Auth::login() werkt
class Gebruiker extends Authenticatable
{
    use HasFactory;

    protected $table = "gebruikers";
    protected $primaryKey = "gebruiker_id";
    public $incrementing = true;
    public $timestamps = true;

    // Eigen kolomnamen in plaats van created_at/updated_at
    const CREATED_AT = 'aangemaakt_op';
    const UPDATED_AT = 'bijgewerkt_op';

    protected $fillable = [
        "naam",
        "email",
        "wachtwoord_hash",
        "status",
        "aangemaakt_op",
        "bijgewerkt_op",
        "suspension_at",
    ];

    // Wachtwoord niet meesturen in json
    protected $hidden = [
        "wachtwoord_hash",
    ];

    protected $casts = [
        'aangemaakt_op' => 'datetime',
        'bijgewerkt_op' => 'datetime',
        'suspension_at' => 'datetime',
    ];

    public function lid()
    {
        return $this->hasOne(Lid::class, 'gebruiker_id', 'gebruiker_id');
    }

    // Een gebruiker kan meerdere rollen hebben
    public function rollen()
    {
        return $this->belongsToMany(Rol::class, 'gebruikers_rollen', 'gebruiker_id', 'rol_id')
            ->withPivot('toegewezen_op');
    }

    public function rol()
    {
        return $this->belongsTo(Rol::class, 'rol_id', 'rol_id');
    }

    public function notificaties()
    {
        return $this->hasMany(Notificatie::class, 'gebruiker_id', 'gebruiker_id');
    }

    // Check of gebruiker een van de rollen heeft
    // Hoofdletter-ongevoelig vanwege bug met 'voorzitter' vs 'Voorzitter'
    public function hasAnyRole($roles): bool
    {
        $roles = is_array($roles) ? $roles : [$roles];

        foreach ($roles as $role) {
            if ($this->rollen()->whereRaw('LOWER(naam) = ?', [strtolower($role)])->exists()) {
                return true;
            }
        }

        return false;
    }

    public function isLid(): bool
    {
        return $this->rollen()->where('naam', 'Lid')->exists();
    }

    public function isApplicatieBeheerder(): bool
    {
        return $this->rollen()
            ->whereRaw('LOWER(naam) = ?', [strtolower('Applicatie Beheerder')])
            ->exists();
    }

    public function isVoorzitter(): bool
    {
        return $this->rollen()->whereRaw('LOWER(naam) = ?', ['voorzitter'])->exists();
    }

    public function isAdminratieMedewerker(): bool
    {
        return $this->rollen()->whereRaw('LOWER(naam) = ?', [strtolower('Administratie Medewerker')])->exists();
    }

    // Hasht het wachtwoord automatisch bij toewijzen
    public function setPasswordAttribute($plainPassword)
    {
        $this->attributes['wachtwoord_hash'] = Hash::make($plainPassword);
    }

    public function verifyPassword($plainPassword): bool
    {
        return Hash::check($plainPassword, $this->wachtwoord_hash);
    }

    // Vertelt Laravel welke kolom het wachtwoord is
    public function getAuthPassword()
    {
        return $this->wachtwoord_hash;
    }

    // check als de suspension_at is nog actief
    public function isSuspended(): bool
    {
       return !is_null($this->suspension_at);
    }

    public function Suspend(): void
    {
        $this->update([
            'status'  => 'Inactief',
            'suspension_at' => now(),
        ]);
    }

    public function UnSuspend(): void
    {
        $this->update([
            'status' => 'Actief',
            'suspension_at' => null,
        ]);
    }

}
