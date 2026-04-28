<?php

namespace App\Services;

use App\Jobs\NotifyLowStockJob;
use App\Models\Product;
use App\Models\Stock;
use App\Models\StockMovement;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class StockService
{
    public function decrease(Product $product, int $qty, string $referenceType, int $referenceId): void
    {
        DB::transaction(function () use ($product, $qty, $referenceType, $referenceId) {
            $stock = Stock::where('product_id', $product->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($stock->quantity < $qty) {
                throw new \Exception('Stock tidak cukup');
            }

            $stock->decrement('quantity', $qty);

            StockMovement::create([
                'product_id' => $product->id,
                'type' => 'OUT',
                'quantity' => $qty,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'note' => 'Sale checkout',
            ]);

            Cache::forget('stocks:list');

            if ($stock->fresh()->quantity < 10) {
                NotifyLowStockJob::dispatch($product->id);
            }
        });
    }

    public function increase(Product $product, int $qty, string $referenceType, int $referenceId): void
    {
        DB::transaction(function () use ($product, $qty, $referenceType, $referenceId) {
            $stock = Stock::firstOrCreate(
                ['product_id' => $product->id],
                ['quantity' => 0]
            );

            $stock->increment('quantity', $qty);

            StockMovement::create([
                'product_id' => $product->id,
                'type' => 'IN',
                'quantity' => $qty,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'note' => 'Restock product',
            ]);

            Cache::forget('stocks:list');
        });
    }
}