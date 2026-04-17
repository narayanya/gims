<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Container extends Model {
    protected $fillable = ['name','code','container_type','capacity','description','status'];
}
