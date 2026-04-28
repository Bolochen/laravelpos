<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Restock;

class RestockService
{
    public function __construct(private StockService $stockService) {}

    public function restock(array $data)
    {
        $product = Product::findOrFail($data['product_id']);

        $restock = Restock::create([
            'product_id' => $product->id,
            'quantity' => $data['quantity'],
            'note' => $data['note'] ?? null,
        ]);

        $this->stockService->increase($product, $data['quantity'], 'RESTOCK', $restock->id);

        return $restock;
    }
}
