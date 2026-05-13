<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Rack extends Model {
    protected $fillable = ['name','code','warehouse_id', 'storage_id','description','status'];
    public function warehouse() { return $this->belongsTo(Warehouse::class); }
    public function storage() { return $this->belongsTo(\App\Models\Storage::class); }
    public function bins()    { return $this->hasMany(Bin::class); }
    /**
     * Get storages in this location
     */
    public function storages()
    {
        return $this->hasMany(\App\Models\Storage::class, 'location_id');
    }
}
