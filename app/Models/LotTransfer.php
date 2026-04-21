<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LotTransfer extends Model
{
    protected $fillable = [
        'lot_id',
        'from_storage_id', 'to_storage_id',
        'from_section_id', 'to_section_id',
        'from_rack_id',    'to_rack_id',
        'from_bin_id',     'to_bin_id',
        'from_container_id','to_container_id',
        'quantity', 'remarks', 'transferred_by',
    ];

    public function lot()         { return $this->belongsTo(Lot::class); }
    public function fromStorage() { return $this->belongsTo(Storage::class, 'from_storage_id'); }
    public function toStorage()   { return $this->belongsTo(Storage::class, 'to_storage_id'); }


// ✅ THIS WAS MISSING
public function fromSection()
{
    return $this->belongsTo(\App\Models\Section::class, 'from_section_id');
}

public function toSection()
{
    return $this->belongsTo(\App\Models\Section::class, 'to_section_id');
}

// ✅ RACK
public function fromRack()
{
    return $this->belongsTo(\App\Models\Rack::class, 'from_rack_id');
}

public function toRack()
{
    return $this->belongsTo(\App\Models\Rack::class, 'to_rack_id');
}

// ✅ BIN
public function fromBin()
{
    return $this->belongsTo(\App\Models\Bin::class, 'from_bin_id');
}

public function toBin()
{
    return $this->belongsTo(\App\Models\Bin::class, 'to_bin_id');
}

// ✅ CONTAINER
public function fromContainer()
{
    return $this->belongsTo(\App\Models\Container::class, 'from_container_id');
}

public function toContainer()
{
    return $this->belongsTo(\App\Models\Container::class, 'to_container_id');
}

// ✅ USER
public function user()
{
    return $this->belongsTo(\App\Models\User::class, 'transferred_by');
}
}

