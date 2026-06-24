<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OldDispatchData extends Model
{
    protected $table = 'old_dispatch_data';

    protected $fillable = [
        'crop',
        'month',
        'year',
        'prefix',
        'sample_id',
        'seed_weight',
        'no_packets',
        'remarks',
        'concerned_person',
        'location',
        'request_date',
        'dispatch_date',
        'tracking_id',
        'courier_service',
    ];

    protected $casts = [
        'request_date'  => 'date',
        'dispatch_date' => 'date',
        'no_packets'    => 'integer',
    ];

    // The table has no created_at / updated_at columns
    public $timestamps = false;
}
