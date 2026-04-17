<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Country extends Model
{
    protected $table = 'core_country';    
    protected $fillable = ['country_name', 'country_code', 'global_region', 'is_active'];
    

    public function states(): HasMany
    {
        return $this->hasMany(State::class);
    }
}
