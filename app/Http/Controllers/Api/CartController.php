<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Services\CartService;
use App\Services\SaleService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(
        private CartService $cartService,
        private SaleService $saleService
    ) {}

    public function show(Request $request)
    {
        return response()->json(
            $this->cartService->getActiveCart($request->user()->id)
        );
    }

    public function addItem(Request $request)
    {
        $data = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        return response()->json([
            'message' => 'Item added to cart',
            'data' => $this->cartService->addItem($request->user()->id, $data),
        ]);
    }

    public function updateItem(Request $request, CartItem $item)
    {
        $data = $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        return response()->json([
            'message' => 'Cart item updated',
            'data' => $this->cartService->updateItem($item, $data['quantity']),
        ]);
    }

    public function removeItem(CartItem $item)
    {
        $this->cartService->removeItem($item);

        return response()->json([
            'message' => 'Cart item removed',
        ]);
    }

    public function checkout(Request $request)
    {
        $cart = $this->cartService->getActiveCart($request->user()->id);

        $sale = $this->saleService->checkout($cart);

        return response()->json([
            'message' => 'Checkout success',
            'data' => $sale->load('items.product'),
        ], 201);
    }
}