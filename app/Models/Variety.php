<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Variety extends Model
{

protected $fillable = [
'crop_id',
'variety_name',
'variety_code',
'variety_type_id',
'breeder_name',
'release_year',
'release_authority',
'description',
'source',
'country_id',
'state_id',
'district_id',
'maturity_duration',
'plant_height',
'grain_type',
'seed_color',
'yield_potential',
'germination_percent',
'purity_percent',
'moisture_percent',
'test_weight',
'disease_resistance',
'pest_resistance',
'drought_tolerance',
'flood_tolerance',
'salinity_tolerance',
'isolation_distance',
'seed_class_id',
'production_region',
'storage_life',
'variety_status'

];

public function crop()
{
    return $this->belongsTo(\App\Models\Crop::class);
}

public function varietyType()
{
    return $this->belongsTo(VarietyType::class);
}

public function seedClass()
{
    return $this->belongsTo(SeedClass::class);
}

public function country()
{
    return $this->belongsTo(Country::class);
}

public function state()
{
    return $this->belongsTo(State::class);
}

public function district()
{
    return $this->belongsTo(District::class);
}

}
