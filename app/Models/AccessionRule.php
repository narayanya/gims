<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccessionRule extends Model
{
    protected $fillable = [
        'name', 'code', 'rule_type', 'description',
        'min_value', 'max_value', 'unit', 'is_mandatory', 'status',
    ];
}
