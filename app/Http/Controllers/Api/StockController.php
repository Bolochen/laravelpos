<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Support\Facades\Cache;

class StockController extends Controller
{
    public function index()
    {
        $stocks = Cache::remember('stocks:list', now()->addMinutes(10), function () {
            return Product::query()
                ->with('stock')
                ->orderBy('name')
                ->get();
        });

        return response()->json($stocks);
    }
}
