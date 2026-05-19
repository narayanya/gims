<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SeedQuality extends Model
{
    protected $fillable = [
        'accession_id',
        'lot_id',
        'germination_percentage',
        'moisture_content',
        'purity_percentage',
        'chlorophyll_percentage',
        'water_level_percentage',
        'viability_test_date',
        'seed_health_status',
        'researcher_id',
        'researcher_other',
        'research_date',
    ];
    protected $casts = [
        'germination_percentage' => 'decimal:2',
        'moisture_content' => 'decimal:2',
        'purity_percentage' => 'decimal:2',
        'chlorophyll_percentage' => 'decimal:2',
        'water_level_percentage' => 'decimal:2',
    ];

    // Relation
    public function accession()
    {
        return $this->belongsTo(Accession::class);
    }

    public function lot()
    {
        return $this->belongsTo(Lot::class);
    }

    public function researcher()
    {
        return $this->belongsTo(User::class, 'researcher_id');
    }
    
}
