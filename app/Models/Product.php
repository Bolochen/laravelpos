<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Product extends Model
{
    use HasFactory;    

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

    public function stock(): HasOne
    {
        return $this->hasOne(Stock::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function restocks(): HasMany
    {
        return $this->hasMany(Restock::class);
    }
}
