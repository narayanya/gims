<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StorageLocation extends Model
{
    protected $fillable = ['name', 'description', 'code', 'warehouse_id'];

    /**
     * Get the warehouse that this location belongs to
     */
    public function warehouse()
    {
        return $this->belongsTo(\App\Models\Warehouse::class);
    }

    /**
     * Get storages in this location
     */
    public function storages()
    {
        return $this->hasMany(\App\Models\Storage::class, 'location_id');
    }
}
