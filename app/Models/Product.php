<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'sku',
        'name',
        'price',
        'minimum_stock',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];
}
