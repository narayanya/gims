<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ActivityLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id', 'action', 'module', 'record_id',
        'record_label', 'old_values', 'new_values',
        'ip_address', 'user_agent',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'created_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Log an activity.
     */
    public static function log(string $action, string $module, $recordId = null, $recordLabel = null, $oldValues = null, $newValues = null): void
    {
        static::create([
            'user_id'      => Auth::id(),
            'action'       => $action,
            'module'       => $module,
            'record_id'    => $recordId,
            'record_label' => $recordLabel,
            'old_values'   => $oldValues,
            'new_values'   => $newValues,
            'ip_address'   => Request::ip(),
            'user_agent'   => Request::userAgent(),
            'created_at'   => now(),
        ]);
    }
}
