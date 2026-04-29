<?php

namespace Tests\Feature\Services;

use App\Jobs\NotifyLowStockJob;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\Stock;
use App\Models\User;
use App\Services\SaleService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class SaleServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_checkout_cart(): void
    {
        Queue::fake();

        Cache::put('report:sales:today', ['cached']);

        $user = User::factory()->create();

        $cart = Cart::factory()->create([
            'user_id' => $user->id,
            'status' => 'ACTIVE',
        ]);

        $product = Product::factory()->create([
            'price' => 10000,
        ]);

        Stock::factory()->create([
            'product_id' => $product->id,
            'quantity' => 20,
        ]);

        CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 3,
            'price' => 10000,
        ]);

        $sale = app(SaleService::class)->checkout($cart->load('items.product'));

        $this->assertDatabaseHas('sales', [
            'id' => $sale->id,
            'user_id' => $user->id,
            'total' => 30000,
        ]);

        $this->assertDatabaseHas('sale_items', [
            'sale_id' => $sale->id,
            'product_id' => $product->id,
            'quantity' => 3,
            'price' => 10000,
            'subtotal' => 30000,
        ]);

        $this->assertDatabaseHas('stocks', [
            'product_id' => $product->id,
            'quantity' => 17,
        ]);

        $this->assertDatabaseHas('carts', [
            'id' => $cart->id,
            'status' => 'CHECKOUT',
        ]);

        $this->assertNull(Cache::get('report:sales:today'));
    }

    public function test_checkout_fails_if_stock_not_enough(): void
    {
        $this->expectException(\Exception::class);

        $user = User::factory()->create();

        $cart = Cart::factory()->create([
            'user_id' => $user->id,
            'status' => 'ACTIVE',
        ]);

        $product = Product::factory()->create([
            'price' => 10000,
        ]);

        Stock::factory()->create([
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 5,
            'price' => 10000,
        ]);

        app(SaleService::class)->checkout($cart->load('items.product'));

        $this->assertDatabaseMissing('sales', [
            'user_id' => $user->id,
        ]);
    }
}
