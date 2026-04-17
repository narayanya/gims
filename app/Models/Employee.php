<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $fillable = ['name', 'company_id', 'status'];

    /**
     * Get accessions that use this category
     */
}
