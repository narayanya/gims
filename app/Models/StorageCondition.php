<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StorageCondition extends Model
{
    protected $fillable = [
        'name',
        'code',
        'temp_min',
        'temp_max',
        'humidity_min',
        'humidity_max',
        'description',
        'status',
    ];

    /**
     * e.g. "-20°C to 4°C"
     */
    public function getTempRangeAttribute(): string
    {
        if ($this->temp_min !== null && $this->temp_max !== null) {
            return $this->temp_min . '°C to ' . $this->temp_max . '°C';
        }
        return '—';
    }

    /**
     * e.g. "30% to 60%"
     */
    public function getHumidityRangeAttribute(): string
    {
        if ($this->humidity_min !== null && $this->humidity_max !== null) {
            return $this->humidity_min . '% to ' . $this->humidity_max . '%';
        }
        return '—';
    }
}
