<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        return Product::with('stock')->latest()->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'sku' => ['required', 'unique:products,sku'],
            'name' => ['required', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'minimum_stock' => ['nullable', 'integer', 'min:1'],
        ]);

        $product = Product::create($data);
        $product->stock()->create(['quantity' => 0]);

        return response()->json($product->load('stock'), 201);
    }

    public function show(Product $product)
    {
        return $product->load('stock');
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'sku' => ['required', 'unique:products,sku,' . $product->id],
            'name' => ['required', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'minimum_stock' => ['nullable', 'integer', 'min:1'],
        ]);

        $product->update($data);

        return response()->json($product->load('stock'));
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return response()->json([
            'message' => 'Product deleted',
        ]);
    }
}