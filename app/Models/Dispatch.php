<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dispatch extends Model
{
    protected $fillable = [
        'dispatch_number',
        'request_id',
        'itn_id',
        'batch_id',
        'accession_id',
        'lot_id',
        'mrn_number',
        'quantity',
        'courier_name',
        'contact_person',
        'contact_number',
        'tracking_number',
        'remarks',
        'dispatched_at',
    ];

    public function request()
    {
        return $this->belongsTo(SeedRequest::class);
    }


    public static function generateDispatchNumber()
    {
        $prefix = 'DISP-' . date('Ymd') . '-';

        $last = self::where('dispatch_number', 'like', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();

        if ($last) {
            $lastNumber = (int) str_replace($prefix, '', $last->dispatch_number);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        return $prefix . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }

    public function accession()
    {
        return $this->belongsTo(Accession::class, 'accession_id');
    }
    public function itn()
    {
        return $this->belongsTo(\App\Models\Itn::class);
    }

    // All warehouse transfers in the same batch
    public function batchTransfers()
    {
        return $this->hasMany(\App\Models\WarehouseTransfer::class, 'batch_id', 'batch_id');
    }
    public function unit()      { return $this->belongsTo(Unit::class, 'capacity_unit_id'); }
}
