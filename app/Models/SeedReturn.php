<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class  SeedReturn extends Model
{
    protected $fillable = [
        'request_id',
        'accession_id',
        'return_type',
        'return_quantity',
        'return_date',
        'remarks',
        'germination_rate',
        'moisture_rate',
    ];

    public function request()
    {
        return $this->belongsTo(SeedRequest::class);
    }

}
