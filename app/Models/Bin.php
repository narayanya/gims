<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Bin extends Model {
    protected $fillable = ['name','code','rack_id','description','status'];
    public function rack() { return $this->belongsTo(Rack::class); }
}
