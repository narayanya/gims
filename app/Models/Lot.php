<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Lot extends Model
{
    protected $fillable = [
        'lot_number', 'reference_number', 'rejuvenation_program', 'prefix', 'sample_id', 'code',
        'accession_id', 'crop_id',
        'storage_id', 'warehouse_id', 'storage_location_id',
        'section_id', 'rack_id', 'bin_id', 'container_id',
       'expiry_date', 'description', 'status',
    ];

    public function lotType()    { return $this->belongsTo(LotType::class); }
    public function seedQualities() { return $this->hasMany(SeedQuality::class, 'lot_id'); }
    public function seedQuantities() { return $this->hasMany(SeedQuantity::class, 'lot_id'); }
    

    public function accession()  { return $this->belongsTo(Accession::class); }
    //public function lotType()    { return $this->belongsTo(LotType::class); }
    public function storage()    { return $this->belongsTo(Storage::class); }
    public function warehouse()  { return $this->belongsTo(Warehouse::class); }
    public function crop()       { return $this->belongsTo(Crop::class); }
    public function variety()    { return $this->belongsTo(Variety::class); }
    public function unit()       { return $this->belongsTo(Unit::class); }

 
    public static function generateLotNumber(
        string $referenceNumber,
        string $rejuvenationProgram,
        string $prefix,
        $sampleId,
        int $rowIndex
    ): string {

        // 👉 Row-wise RP (for display only)
        $rpWithRow = "{$rejuvenationProgram}/" . ($rowIndex + 1);

        // 👉 Base WITHOUT LOT
        $sequenceBase = "{$referenceNumber}-{$rejuvenationProgram}-{$prefix}-{$sampleId}-";

        // Find last sequence
        $last = self::where('lot_number', 'like', $sequenceBase . '%')
            ->orderByRaw("CAST(SUBSTRING_INDEX(lot_number, '-', -1) AS UNSIGNED) DESC")
            ->first();

        $seq = 1;

        if ($last) {
            preg_match('/-(\d{2})$/', $last->lot_number, $matches);
            $seq = isset($matches[1]) ? ((int)$matches[1] + 1) : 1;
        }

        // 👉 Final number (no LOT text)
        return "{$referenceNumber}-{$rpWithRow}-{$prefix}-{$sampleId}-" 
            . str_pad($seq, 2, '0', STR_PAD_LEFT);
    }
    

}