<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LotRegeneration extends Model
{
     protected $fillable = [
        'lot_id',
        'type',
        'date',
        'reason',
        'status'
    ];

    public function lot()
    {
        return $this->belongsTo(Lot::class);
    }
}
