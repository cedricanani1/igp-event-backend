<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Categorie extends Model
{
    use HasFactory,SoftDeletes;
    protected $guarded = [
        'id',
    ];
    public function products(){
        return $this->hasMany(Product::class,'categorie_id');
    }
    public function child(){
        return $this->hasMany(Categorie::class,'parent_id');
    }
    public function parent(){
        return $this->belongsTo(Categorie::class,'parent_id');
    }
}
