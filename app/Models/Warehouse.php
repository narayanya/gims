<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    protected $fillable = ['name', 'description', 'code', 'country_id', 'state_id', 'district_id', 'city_id', 'status'];

    /**
     * Get storages in this warehouse
     */
    public function storages()
    {
        return $this->hasMany(\App\Models\Storage::class, 'warehouse_id');
    }
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
