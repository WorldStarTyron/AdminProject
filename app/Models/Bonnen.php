<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bonnen extends Model
{
    public $timestamps = false;
    protected $primaryKey = 'bon_id';
    
   protected $fillable = [
    'betaling_id',
    'bon_nummer',
    'beschrijving',
    'aangemaakt_op',
];

    protected $table = 'bonnen';


    public function betaling()
    {
        return $this->belongsTo(Betaling::class, 'betaling_id');
    }
}
