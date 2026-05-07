<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestTransaction extends Model
{
    protected $fillable = [
        'request_id',
        'transaction_type',
        'lot_id',
        'crop_id',
        'accession_id',
        'quantity',
        'old_quantity',
        'old_quantity_show',
        'new_quantity',
        'new_quantity_show',
        'reference_no',
        'unit_id',
        'remarks',
        'user_id',
        'created_by',
        'updated_by',
    ];
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
