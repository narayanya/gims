<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StorageTime extends Model
{
    protected $fillable = [
        'name',
        'code',
        'duration_value',
        'duration_unit',
        'description',
        'status',
    ];

    /**
     * Human-readable duration label e.g. "6 Months"
     */
    public function getDurationLabelAttribute(): string
    {
        if ($this->duration_value && $this->duration_unit) {
            return $this->duration_value . ' ' . ucfirst($this->duration_unit);
        }
        return '—';
    }
}
