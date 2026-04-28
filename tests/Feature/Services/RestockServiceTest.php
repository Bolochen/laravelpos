<?php

namespace Tests\Feature\Services;

use App\Models\Product;
use App\Services\RestockService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RestockServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_restock_product(): void
    {
        $product = Product::factory()->create();

        $restock = app(RestockService::class)->restock([
            'product_id' => $product->id,
            'quantity' => 50,
            'note' => 'Initial stock',
        ]);

        $this->assertDatabaseHas('restocks', [
            'id' => $restock->id,
            'product_id' => $product->id,
            'quantity' => 50,
        ]);

        $this->assertDatabaseHas('stocks', [
            'product_id' => $product->id,
            'quantity' => 50,
        ]);

        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $product->id,
            'type' => 'IN',
            'quantity' => 50,
            'reference_type' => 'RESTOCK',
            'reference_id' => $restock->id,
        ]);
    }
}
