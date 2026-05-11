<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pouch extends Model
{
    protected $fillable = ['name', 'code', 'length', 'width', 'height', 'dimension_unit', 'description', 'status'];
}
