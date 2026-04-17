<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Crop extends Model
{
    protected $table = 'core_crop';    
    protected $fillable = [
        'crop_name',
        'crop_code',
        'vertical_id',
        'numeric_code',
        'effective_date',
        'crop_flag',
        'focus_code',
        'scientific_name',
        'common_name',
        'category_id',
        'crop_category_id',
        'crop_type_id',
        'season_id',
        'description',
        'family_name',
        'genus',
        'species',
        'duration_days',
        'sowing_time',
        'harvest_time',
        'climate_requirement',
        'soil_type_id',
        'isolation_distance',
        'expected_yield',
        'is_active',
        'update_status',
    ];

    /**
     * Get accessions that use this crop
     */
    public function accessions()
    {
        return $this->hasMany(\App\Models\Accession::class, 'crop_id');
    }
    public function cropCategory()
    {
        return $this->belongsTo(CropCategory::class,'crop_category_id');
    }

    public function cropType()
    {
        return $this->belongsTo(CropType::class,'crop_type_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class,'category_id');
    }

    public function season()
    {
        return $this->belongsTo(Season::class,'season_id');
    }
    public function soilType()
    {
        return $this->belongsTo(SoilType::class);
    }

}
