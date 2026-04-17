<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Storage extends Model
{
    protected $fillable = [
        'storage_id',
        'name',
        'storage_time_id',
        'storage_condition_id',
        'storage_type_id',
        'warehouse_id',
        'location',
        'capacity',
        'unit_id', 
        'current_usage',
        'temperature',
        'humidity',
        'description',
        'image',
        'status',
    ];

    protected $casts = [
        'capacity' => 'decimal:2',
        'current_usage' => 'decimal:2',
    ];

    /**
     * Get the user who manages this storage.
     */
    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'managed_by');
    }

    /**
     * Get the available capacity.
     */
    /*public function getAvailableCapacityAttribute(): float
    {
        return $this->capacity - $this->current_usage;
    }*/

    /**
     * Get the usage percentage.
     */
    /*public function getUsagePercentageAttribute(): float
    {
        if ($this->capacity <= 0) {
            return 0;
        }
        return round(($this->current_usage / $this->capacity) * 100, 2);
    }*/

    /**
     * Check if storage is at capacity.
     */
    public function isAtCapacity(): bool
    {
        return $this->current_usage >= $this->capacity;
    }

    /**
     * Generate a unique storage ID.
     */
    /*public static function generateStorageId(): string
    {
        do {
            $id = 'STG-' . date('Y') . '-' . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
        } while (self::where('storage_id', $id)->exists());

        return $id;
    }*/
    public static function generateStorageId(string $storageTimeCode): string
        {
            $year = date('Y');

            // Get last record for same year + storage type
            $lastRecord = self::where('storage_id', 'like', "STG-$year-$storageTimeCode-%")
                ->orderBy('storage_id', 'desc')
                ->first();

            if ($lastRecord) {
                // Extract last 3 digits (001, 002...)
                $lastNumber = (int) substr($lastRecord->storage_id, -3);
                $nextNumber = $lastNumber + 1;
            } else {
                $nextNumber = 1;
            }

            // Always 3-digit format
            $sequence = str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

            return "STG-$year-$storageTimeCode-$sequence";
        }
    public function storageTime()
    {
        return $this->belongsTo(StorageTime::class);
    }

    public function storageCondition()
    {
        return $this->belongsTo(StorageCondition::class);
    }
    public function storageWarehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }
    public function storageType()
    {
        return $this->belongsTo(StorageType::class, 'storage_type_id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }
    public function lots()
    {
        return $this->hasMany(Lot::class, 'storage_id');
    }



    public function getUsedQuantityAttribute()
    {
        return $this->lots()->sum('quantity');
    }

    public function getAvailableCapacityAttribute()
    {
        return $this->capacity - $this->used_quantity;
    }

   
    // Used quantity (from lots)
    public function getCurrentUsageAttribute()
    {
        return $this->lots()->sum('quantity') ?? 0;
    }

    // Usage percentage
    public function getUsagePercentageAttribute()
    {
        if (!$this->capacity || $this->capacity == 0) {
            return 0;
        }

        return round(($this->current_usage / $this->capacity) * 100, 2);
    }


}
