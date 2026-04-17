<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dispatch extends Model
{
    protected $fillable = [
        'dispatch_number',    
    'request_id',
        'accession_id',
        'mrn_number',
        'quantity',
        'courier_name',
        'contact_person',
        'contact_number',
        'tracking_number',
        'remarks',
        'dispatched_at'
    ];

    public function request()
    {
        return $this->belongsTo(SeedRequest::class);
    }

    public static function generateDispatchNumber()
    {
        $last = self::latest()->first();

        if (!$last) {
            return 'DISP-00001';
        }

        $number = intval(substr($last->dispatch_number, 5)) + 1;

        return 'DISP-' . str_pad($number, 5, '0', STR_PAD_LEFT);
    }

    public function accession()
    {
        return $this->belongsTo(Accession::class, 'accession_id');
    }
}
