<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class  SeedRequest extends Model
{
    protected $table = 'requests';
    protected $fillable = [
        'user_id',    
        'request_number',
        'request_through',
        'crop_id',
        'accession_id',
        'quantity',
        'unit_id',
        'requester_name',
        'requester_email',
        'purpose',
        'purpose_details',
        'status',
        'request_date',
        'required_date',
        'notes',
        'approved_by',
        'approved_at',
        'remarks',
        'receive_status',
        'receive_remarks',
        'receive_date',
        'return_quantity',
        'return_remarks',
        'return_date',
    ];

    protected $casts = [
        'request_date'  => 'date',
        'required_date' => 'date',
        'approved_at'   => 'datetime',
        'receive_date'  => 'date',
        'return_date'   => 'date',
    ];

    public function user()
{
    return $this->belongsTo(User::class);
}

    public function crop(): BelongsTo
    {
        return $this->belongsTo(Crop::class);
    }


    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
    public function accession()
    {
        return $this->belongsTo(Accession::class);
    }

    public static function generateRequestNumber(): string
    {
        $lastRequest = self::latest('id')->first();
        $number = $lastRequest ? intval(substr($lastRequest->request_number, 4)) + 1 : 1;
        return 'REQ-' . str_pad($number, 5, '0', STR_PAD_LEFT);
    }
    public function dispatches()
    {
        return $this->hasMany(Dispatch::class, 'request_id');
    }
    public function employee() {
        return $this->belongsTo(Employee::class, 'employee_id', 'employee_id');
    }

}
