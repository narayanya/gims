<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Container extends Model
{
    protected $fillable = [
        'name', 'code', 'container_type',
        'length', 'width', 'height', 'dimension_unit',
        'capacity', 'unit_id',
        'bin_id',
        'description', 'status',
    ];

    public function unit()
    {
        return $this->belongsTo(\App\Models\Unit::class);
    }

    public function bin()
    {
        return $this->belongsTo(\App\Models\Bin::class);
    }
}
