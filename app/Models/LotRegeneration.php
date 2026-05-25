<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LotRegeneration extends Model
{
     protected $fillable = [
        'lot_id',
    'old_regen_year',
    'old_expiry_date',
    'old_regeneration_date',

    'regen_year',
    'expiry_date',
    'regeneration_date',
    'reason',
    'status',
    ];

    public function lot()
    {
        return $this->belongsTo(Lot::class);
    }
}
