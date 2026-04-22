<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Container extends Model {
    protected $fillable = ['name','code','container_type','capacity', 'unit_id','description','status'];
    public function unit()
    {
        return $this->belongsTo(\App\Models\Unit::class);
    }
}

