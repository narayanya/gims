<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QualityMaster extends Model
{
    protected $table = 'quality_master';     
    protected $fillable = [
        'qc_code',
        'qc_name',
        'description',
        'is_active'
    ];
}
