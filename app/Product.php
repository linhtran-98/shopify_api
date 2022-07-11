<?php

namespace App;

use App\Image;
use App\Variant;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'id', 'title', 'description', 'image', 'price', 'shop_id'
    ];
}
