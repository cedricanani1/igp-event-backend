<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory,SoftDeletes;
    protected $guarded = [
        'id',
    ];

    public function type(){
        return $this->belongsTo(Categorie::class,'categorie_id');
    }
    public function rate(){
        return $this->hasMany(Rating::class,'product_id');
    }
    public function photo(){
        return $this->hasMany(Photo::class,'product_id');
    }
    public function order(){
        return $this->belongsToMany(Order::class,'carts','product_id','order_id');
    }
}
