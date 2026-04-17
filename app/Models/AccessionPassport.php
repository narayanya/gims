<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccessionPassport extends Model
{
    protected $fillable = [
        'accession_id',
        'sample_name',
        'passport_no',
        'remarks'
    ];

    public function accession()
    {
        return $this->belongsTo(Accession::class);
    }
}
