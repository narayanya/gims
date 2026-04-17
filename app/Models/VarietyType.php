<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VarietyType extends Model
{
    protected $fillable = [
        'name',
        'code',
        'description',
    ];

    public function varieties(): HasMany
    {
        return $this->hasMany(Variety::class, 'type_id');
    }
}
