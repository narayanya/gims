<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Section extends Model {
    protected $fillable = ['name', 'unit_id', 'code','description','status'];
    public function racks() { return $this->hasMany(Rack::class); }
    public function unit() { return $this->belongsTo(Unit::class); }
}
