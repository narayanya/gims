<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name', 'description', 'code', 'status'];

    /**
     * Get accessions that use this category
     */
    public function accessions()
    {
        return $this->hasMany(\App\Models\Accession::class, 'category_id');
    }
}
