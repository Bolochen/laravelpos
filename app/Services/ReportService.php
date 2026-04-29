<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Sale;
use Illuminate\Support\Facades\Cache;

class ReportService
{
    public function stockReport()
    {
        return Cache::remember('report:stocks', now()->addMinutes(10), function () {
            return Product::query()
                ->join('stocks', 'products.id', '=', 'stocks.product_id')
                ->select('products.id', 'products.name', 'stocks.quantity')
                ->orderBy('products.name')
                ->get();
        });
    }

    public function todaySales()
    {
        return Cache::remember('report:sales:today', now()->addMinutes(5), function () {
            return Sale::whereDate('created_at', today())
                ->with('items.product')
                ->get();
        });
    }
}