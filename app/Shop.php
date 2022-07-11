<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    public $timestamps = false;
    
    protected $fillable = [
        'id',
        'name',
        'domain',
        'email',
        'shopify_domain',
        'access_token',
        'plan',
        'created_at'
    ];
}
