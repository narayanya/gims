<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CropType extends Model
{
    protected $fillable = [
        'name',
        'code',
        'description',
        'status'
    ];

    public function crops(): HasMany
    {
        return $this->hasMany(Crop::class, 'type_id');
    }
}
