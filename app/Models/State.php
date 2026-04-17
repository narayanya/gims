<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class State extends Model
{
    protected $table = 'core_state';    
    protected $fillable = [
        'state_name', 'country_id', 'state_code', 'short_code', 'effective_date', 'is_active',
    ];

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function districts(): HasMany
    {
        return $this->hasMany(District::class);
    }

    public function cities(): HasMany
    {
        return $this->hasMany(City::class);
    }
    
}
