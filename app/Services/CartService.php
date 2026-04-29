<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;

class CartService
{
    public function getActiveCart(int $userId): Cart
    {
        return Cart::firstOrCreate([
            'user_id' => $userId,
            'status' => 'ACTIVE',
        ])->load('items.product');
    }

    public function addItem(int $userId, array $data): CartItem
    {
        $cart = $this->getActiveCart($userId);
        $product = Product::findOrFail($data['product_id']);

        $item = $cart->items()
            ->where('product_id', $product->id)
            ->first();

        if ($item) {
            $item->increment('quantity', $data['quantity']);
        } else {
            $item = $cart->items()->create([
                'product_id' => $product->id,
                'quantity' => $data['quantity'],
                'price' => $product->price,
            ]);
        }

        return $item->refresh()->load('product');
    }

    public function updateItem(CartItem $item, int $quantity): CartItem
    {
        $item->update([
            'quantity' => $quantity,
        ]);

        return $item->refresh()->load('product');
    }

    public function removeItem(CartItem $item): void
    {
        $item->delete();
    }
}
