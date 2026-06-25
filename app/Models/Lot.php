<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lot extends Model
{
    protected $fillable = [
        'lot_number',
        'arrival_type',
        'dispatch_id',
        'reference_number',
        'rejuvenation_program', 
        'prefix', 
        'sample_id',
        'unit_id',
        'storage_id',
        'section_id',
        'rack_id', 
        'bin_id', 
        'container_id',
        'expiry_date',
        'accession_id', 
        'crop_id',
        'description', 
        'status', 
        'storage_location_id', 'batch_number', 
        'dispose_date',
        'dispose_type',
        'dispose_reason',
        'regeneration_date',
        'regen_year'
    ];

    public function accession()     { return $this->belongsTo(Accession::class); }
    public function lotMaster()     { return $this->belongsTo(LotMaster::class, 'lot_master_id'); }
    public function lotType()       { return $this->belongsTo(LotType::class); }
    public function storage()
{
    return $this->belongsTo(Storage::class, 'storage_id');
}

    public function crop()          { return $this->belongsTo(Crop::class, 'crop_id'); }
    
    public function unit()          { return $this->belongsTo(Unit::class); }
    public function seedQualities() { return $this->hasMany(SeedQuality::class, 'lot_id'); }
    public function seedQuantities(){ return $this->hasMany(SeedQuantity::class, 'lot_id'); }
    public function seedQuality()
{
    return $this->hasOne(SeedQuality::class, 'lot_id');
}

    public static function generateLotNumber(): string
    {
        $year   = date('Y');
        $prefix = 'LOT-' . $year . '-';
        $last   = self::where('lot_number', 'like', $prefix . '%')
                      ->orderByDesc('lot_number')->first();
        $seq = $last ? ((int) substr($last->lot_number, -5)) + 1 : 1;
        return $prefix . str_pad($seq, 5, '0', STR_PAD_LEFT);
    }
    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function rack()
    {
        return $this->belongsTo(Rack::class);
    }

    public function bin()
    {
        return $this->belongsTo(Bin::class);
    }

    public function container()
    {
        return $this->belongsTo(Container::class);
    }

    public function regunDisposes()
    {
        return $this->hasMany(LotRegeneration::class);
    }
}
