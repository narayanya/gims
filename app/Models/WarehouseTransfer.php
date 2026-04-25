<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WarehouseTransfer extends Model
{
    protected $fillable = [
        'batch_id', 'lot_id', 'crop_id', 'accession_id', 'from_storage_id', 'to_storage_id', 'quantity',
        'from_warehouse_id',
        'to_warehouse_id',
        'transferred_by',
        'remarks',
        'status',
    ];
    public function lot()
    {
        return $this->belongsTo(Lot::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    
    public function fromStorage() { return $this->belongsTo(Storage::class, 'from_storage_id'); }
    public function toStorage()   { return $this->belongsTo(Storage::class, 'to_storage_id'); }

    public function fromWarehouse() { return $this->belongsTo(Warehouse::class, 'from_warehouse_id'); }
    public function toWarehouse()   { return $this->belongsTo(Warehouse::class, 'to_warehouse_id'); }

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
public function itn()
{
    return $this->hasOne(Itn::class, 'transfer_id');
}

// ITN linked via batch_id (for multi-lot transfers)
public function batchItn()
{
    return $this->hasOneThrough(
        Itn::class,
        WarehouseTransfer::class,
        'batch_id', // foreign key on warehouse_transfers
        'batch_id', // foreign key on itns
        'batch_id', // local key on this model
        'batch_id'  // local key on warehouse_transfers
    );
}

// Resolve the ITN for this transfer (single or batch)
public function getItnAttribute()
{
    return Itn::where('batch_id', $this->batch_id)->first()
        ?? $this->itn()->first();
}
   
}

