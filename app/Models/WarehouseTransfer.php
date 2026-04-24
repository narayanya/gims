<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WarehouseTransfer extends Model
{
    protected $fillable = [
        'lot_id', 'crop_id', 'accession_id', 'from_storage_id', 'to_storage_id', 'quantity',
    'from_warehouse_id',
    'to_warehouse_id',
    'transferred_by',
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
   
}

