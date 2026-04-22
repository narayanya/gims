<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LotTransfer extends Model
{
    protected $fillable = [
        
    ];

public function country() {
    return $this->belongsTo(Country::class);
    }

    public function state() {
        return $this->belongsTo(State::class);
    }

    public function district() {
        return $this->belongsTo(District::class);
    }

    public function city() {
        return $this->belongsTo(City::class);
    }
   
}

