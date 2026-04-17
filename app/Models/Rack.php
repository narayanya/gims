<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Rack extends Model {
    protected $fillable = ['name','code','section_id','description','status'];
    public function section() { return $this->belongsTo(Section::class); }
    public function bins()    { return $this->hasMany(Bin::class); }
}
