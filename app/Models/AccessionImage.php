<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccessionImage extends Model
{
    protected $fillable = ['accession_id', 'image_name', 'is_primary', 'sort_order'];

    public function accession()
    {
        return $this->belongsTo(Accession::class);
    }

    public function getUrlAttribute(): string
    {
        return asset('storage/accessions/images/' . $this->image_name);
    }
}
