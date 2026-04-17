<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LotType extends Model
{
    protected $fillable = ['name', 'code', 'description', 'status'];
}
