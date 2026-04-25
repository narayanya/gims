<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Itn extends Model
{
    protected $fillable = [
        'transfer_id',
        'batch_id',
        'itn_number',
        'itn_date',
        'lot_id',
        'crop_id',
        'accession_id',
        'from_warehouse_id',
        'to_warehouse_id',
        'from_storage_id',
        'to_storage_id',
        'quantity',

        'receiver',
        'mobile_number',
        'email',
        'instructions',
        'photo',
        'created_by'
    ];

    public function transfer()
    {
        return $this->belongsTo(WarehouseTransfer::class);
    }

    // All transfers in the same batch
    public function transfers()
    {
        return $this->hasMany(WarehouseTransfer::class, 'batch_id', 'batch_id');
    }
    public function lot()
{
    return $this->belongsTo(Lot::class);
}

public function crop()
{
    return $this->belongsTo(Crop::class);
}

public function accession()
{
    return $this->belongsTo(Accession::class);
}

public function fromWarehouse()
{
    return $this->belongsTo(Warehouse::class, 'from_warehouse_id');
}

public function toWarehouse()
{
    return $this->belongsTo(Warehouse::class, 'to_warehouse_id');
}

public function fromStorage() {
    return $this->belongsTo(Storage::class, 'from_storage_id');
}

public function toStorage() {
    return $this->belongsTo(Storage::class, 'to_storage_id');
}
public function unit()      { return $this->belongsTo(Unit::class, 'capacity_unit_id'); }

}
