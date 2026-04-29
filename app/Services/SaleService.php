<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Sale;
use App\Services\StockService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class SaleService
{
    public function __construct(private StockService $stockService) {}

    public function checkout(Cart $cart)
    {
        return DB::transaction(function () use ($cart) {
            $cart->loadMissing('items.product');

            if ($cart->items->isEmpty()) {
                throw new \Exception('Cart is empty');
            }

            $sale = Sale::create([
                'invoice_no' => 'INV-' . now()->format('YmdHis'),
                'user_id' => $cart->user_id,
                'total' => $cart->items->sum(fn ($item) => $item->quantity * $item->price),
            ]);

            foreach ($cart->items as $item) {
                $sale->items()->create([
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'subtotal' => $item->quantity * $item->price,
                ]);

                $this->stockService->decrease(
                    $item->product,
                    $item->quantity,
                    'SALE',
                    $sale->id
                );
            }

            $cart->update(['status' => 'CHECKOUT']);

            Cache::forget('report:sales:today');

            return $sale;
        });
    }
}
