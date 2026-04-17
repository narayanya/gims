<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LotMaster extends Model
{
    protected $table = 'lot_masters';

    protected $fillable = ['name', 'code', 'lot_type_id', 'description', 'status'];

    public function lotType()
    {
        return $this->belongsTo(LotType::class);
    }

    public function lots()
    {
        return $this->hasMany(Lot::class, 'lot_master_id');
    }
}
