<?php

namespace Tests\Feature\Services;

use App\Jobs\NotifyLowStockJob;
use App\Models\Product;
use App\Models\Stock;
use App\Models\StockMovement;
use App\Services\StockService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class StockServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_increase_stock(): void
    {
        Cache::put('stocks:list', ['cached']);

        $product = Product::factory()->create();

        app(StockService::class)->increase($product, 20, 'RESTOCK', 1);

        $this->assertDatabaseHas('stocks', [
            'product_id' => $product->id,
            'quantity' => 20,
        ]);

        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $product->id,
            'type' => 'IN',
            'quantity' => 20,
            'reference_type' => 'RESTOCK',
            'reference_id' => 1,
        ]);

        $this->assertNull(Cache::get('stocks:list'));
    }

    public function test_can_decrease_stock(): void
    {
        Queue::fake();

        $product = Product::factory()->create();
        Stock::factory()->create([
            'product_id' => $product->id,
            'quantity' => 20,
        ]);

        app(StockService::class)->decrease($product, 5, 'SALE', 1);

        $this->assertDatabaseHas('stocks', [
            'product_id' => $product->id,
            'quantity' => 15,
        ]);

        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $product->id,
            'type' => 'OUT',
            'quantity' => 5,
            'reference_type' => 'SALE',
            'reference_id' => 1,
        ]);

        Queue::assertNotPushed(NotifyLowStockJob::class);
    }

    public function test_dispatches_low_stock_job_when_stock_below_10(): void
    {
        Queue::fake();

        $product = Product::factory()->create();

        Stock::factory()->create([
            'product_id' => $product->id,
            'quantity' => 12,
        ]);

        app(StockService::class)->decrease($product, 5, 'SALE', 1);

        Queue::assertPushed(NotifyLowStockJob::class);
    }

    public function test_cannot_decrease_when_stock_not_enough(): void
    {
        $this->expectException(\Exception::class);

        $product = Product::factory()->create();

        Stock::factory()->create([
            'product_id' => $product->id,
            'quantity' => 3,
        ]);

        app(StockService::class)->decrease($product, 10, 'SALE', 1);
    }
}
