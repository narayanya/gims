<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SeedQuantity extends Model
{
    protected $table = 'seed_quantities';

    protected $fillable = [
        'accession_id',
        'lot_id',
        'reference_number',
        'number_of_seeds',
        'per_seed_weight',
        'quantity',
        'capacity_unit_id',
        'quantity_show',
        'min_quantity',
        'in_seed',
        'out_seed',
        'return_seed',
    ];

    public function accession() { return $this->belongsTo(Accession::class); }
    public function lot()       { return $this->belongsTo(Lot::class); }
    public function unit()      { return $this->belongsTo(Unit::class, 'capacity_unit_id'); }
    
}
