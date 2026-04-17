<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class Accession extends Model
{
    protected $fillable = [
        // Basic Information
        'accession_number',
        'accession_name',
        'acc_source',
        'ext_source',
        'requester_show',
        'crop_id',

        
        // Collection Information
        'collection_number',
        'collection_date',
        'collector_name',
        'donor_name',
        'collection_site',
        'country_id',
        'state_id',
        'district_id',
        'city_id',
        'latitude',
        'longitude',
        'altitude',
        'pincode',
        
        // Biological/Genetic Information
        'biological_status',
        'sample_type',
        'reproductive_type',
        
        // Quantity Information
        'quantity',
        'capacity_unit_id',
        'quantity_show',
        
        // Storage Information
        'warehouse_id',
        'storage_location_id',
        'storage_time_id',
        'storage_time',
        'storage_condition_id',
        'storage_type_id',
        
        // Documentation
        'barcode_type',
        'barcode',
        'image_path',
        'passport_file_path',
        'notes',
        
        // System Fields
        'entry_date',
        'entered_by',
        'status',
        'created_by',
         'recheck_date',
        'expiry_date',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'collection_date' => 'date',
        'entry_date' => 'date',
        'recheck_date' => 'date',
        'expiry_date' => 'date',
    ];

    /**
     * Get the crop associated with this accession.
     */
    public function crop(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Crop::class);
    }

    /**
     * Get the variety associated with this accession.
     */
    public function variety(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Variety::class);
    }
    public function country()
    {
        return $this->belongsTo(Country::class);
    }
    
    /**
     * Get the state associated with this accession.
     */
    public function state(): BelongsTo
    {
        return $this->belongsTo(\App\Models\State::class);
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(\App\Models\District::class);
    }
    public function city()
    {
        return $this->belongsTo(City::class);
    }

    /**
     * Get the warehouse associated with this accession.
     */
    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Warehouse::class);
    }

    /**
     * Get the storage location associated with this accession.
     */
    public function storageLocation(): BelongsTo
    {
        return $this->belongsTo(\App\Models\StorageLocation::class);
    }

    /**
     * Get the storage type associated with this accession.
     */
    public function storageType(): BelongsTo
    {
        return $this->belongsTo(\App\Models\StorageType::class);
    }

    /**
     * Get the capacity unit associated with this accession.
     */
    public function capacityUnit(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Unit::class, 'capacity_unit_id');
    }

    /**
     * Get the user who created this accession.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the formatted quantity with unit.
     */
    public function getFormattedQuantityAttribute(): string
    {
        return number_format($this->quantity, 2) . ' ' . $this->unit;
    }

    /**
     * Get the status badge color.
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'active' => 'success',
            'inactive' => 'secondary',
            'quarantine' => 'warning',
            'depleted' => 'danger',
            'testing' => 'info',
            default => 'secondary',
        };
    }

    /**
     * Get the priority badge color.
     */
    public function getPriorityColorAttribute(): string
    {
        return match($this->priority) {
            'normal' => 'secondary',
            'high' => 'warning',
            'critical' => 'danger',
            default => 'secondary',
        };
    }

    /**
     * Check if accession is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if accession is depleted.
     */
    public function isDepleted(): bool
    {
        return $this->status === 'depleted';
    }

    /**
     * Generate a unique accession ID.
     */
    public static function generateAccessionId(): string
    {
        do {
            $id = 'ACC-' . date('Y') . '-' . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
        } while (self::where('accession_id', $id)->exists());

        return $id;
    }

    /**
     * Scope for active accessions.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for accessions by crop.
     */
    public function scopeByCrop($query, $crop)
    {
        return $query->where('crop', $crop);
    }

    /**
     * Scope for accessions by warehouse.
     */
    public function scopeByWarehouse($query, $warehouse)
    {
        return $query->where('warehouse', $warehouse);
    }
   
    public function getPhotoUrlAttribute()
    {
        return $this->image_path
            ? asset('storage/accessions/images/' . $this->image_path)
            : null;
    }

    public function storageTime()
    {
        return $this->belongsTo(StorageTime::class, 'storage_time_id');
    }

    public function storageCondition()
    {
        return $this->belongsTo(StorageCondition::class, 'storage_condition_id');
    }
    public function passports()
    {
        return $this->hasMany(\App\Models\AccessionPassport::class);
    }

    public function images()
    {
        return $this->hasMany(AccessionImage::class)->orderBy('sort_order');
    }
    public function seedQualities()
    {
        return $this->hasMany(SeedQuality::class);
    }
    public function seedQuantities()
    {
        return $this->hasMany(SeedQuantity::class);
    }
    
   /* public static function generateAccessionNumber(string $cropCode)
    {
        do {
            $id = $cropCode . date('Y') .  '-ACC-' .
                str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);

        } while (self::where('accession_number', $id)->exists());

        return $id;
    }*/

   /* public static function generateAccessionId($crop): string
    {
        do {
            $cropCode = $crop->crop_code; // 👈 use crop_code

            $id = $cropCode . '-ACC-' . date('Y') . '-' . 
                str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);

        } while (self::where('accession_id', $id)->exists());

        return $id;
    }*/
}
