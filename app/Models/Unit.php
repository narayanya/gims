<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    protected $fillable = ['name', 'description', 'code','status'];

    /**
     * Get accessions that use this unit
     */
    public function accessions()
    {
        return $this->hasMany(\App\Models\Accession::class, 'unit_id');
    }
}
