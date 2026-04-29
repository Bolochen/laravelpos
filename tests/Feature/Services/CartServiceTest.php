<?php

namespace Tests\Feature\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use App\Services\CartService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_get_active_cart(): void
    {
        $user = User::factory()->create();

        $cart = app(CartService::class)->getActiveCart($user->id);

        $this->assertDatabaseHas('carts', [
            'id' => $cart->id,
            'user_id' => $user->id,
            'status' => 'ACTIVE',
        ]);
    }

    public function test_can_add_item_to_cart(): void
    {
        $user = User::factory()->create();

        $product = Product::factory()->create([
            'price' => 15000,
        ]);

        $item = app(CartService::class)->addItem($user->id, [
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        $this->assertDatabaseHas('cart_items', [
            'id' => $item->id,
            'product_id' => $product->id,
            'quantity' => 2,
            'price' => 15000,
        ]);
    }

    public function test_add_same_product_updates_quantity(): void
    {
        $user = User::factory()->create();

        $product = Product::factory()->create([
            'price' => 10000,
        ]);

        $service = app(CartService::class);

        $service->addItem($user->id, [
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        $service->addItem($user->id, [
            'product_id' => $product->id,
            'quantity' => 3,
        ]);

        $this->assertDatabaseHas('cart_items', [
            'product_id' => $product->id,
            'quantity' => 5,
        ]);
    }

    public function test_can_update_cart_item_quantity(): void
    {
        $item = CartItem::factory()->create([
            'quantity' => 2,
        ]);

        app(CartService::class)->updateItem($item, 7);

        $this->assertDatabaseHas('cart_items', [
            'id' => $item->id,
            'quantity' => 7,
        ]);
    }

    public function test_can_remove_cart_item(): void
    {
        $item = CartItem::factory()->create();

        app(CartService::class)->removeItem($item);

        $this->assertDatabaseMissing('cart_items', [
            'id' => $item->id,
        ]);
    }
}