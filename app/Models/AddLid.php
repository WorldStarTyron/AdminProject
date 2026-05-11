<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AddLid extends Model
{
    use HasFactory;

    protected $fillable = [
        'Naam',
        'telefoonnummer',
        'adres',
        'woonplaats',
        'geboortedatum',
        'email',
        'lid_sinds'
    ];

    protected $table = 'leden';
    protected $primaryKey = 'lid_id';
    public $incrementing = false;
    protected $keyType = 'string';

    const CREATED_AT = 'lid_sinds';
    const UPDATED_AT = 'bijgewerkt_op';

    public static function generateMemberId()
    {
        // Haalt de laatste ID op en telt er 1 op
        $lastMember = self::orderBy('lid_id', 'desc')->first();
        $lastId = $lastMember ? intval($lastMember->lid_id) : 0;
        return strval($lastId + 1);
    }
}
