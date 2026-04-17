<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StorageType extends Model
{
    protected $fillable = ['name', 'description'];

    /**
     * Accessions that use this storage type (if applicable)
     */
    public function accessions()
    {
        return $this->hasMany(\App\Models\Accession::class, 'warehouse', 'name');
    }
}
