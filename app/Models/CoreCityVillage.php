<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CoreCityVillage extends Model
{
    protected $table = 'core_city_village';

    protected $fillable = [
        'state_id', 'district_id', 'division_name',
        'city_village_name', 'city_village_code',
        'pincode', 'longitude', 'latitude',
        'is_active', 'effective_date',
    ];

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }
}
