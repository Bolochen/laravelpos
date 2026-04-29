<?php

namespace Tests\Feature\Services;

use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Stock;
use App\Services\ReportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class ReportServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_get_stock_report_and_cache_it(): void
    {
        Cache::flush();

        $product = Product::factory()->create([
            'name' => 'Indomie',
        ]);

        Stock::factory()->create([
            'product_id' => $product->id,
            'quantity' => 25,
        ]);

        $result = app(ReportService::class)->stockReport();

        $this->assertCount(1, $result);
        $this->assertEquals('Indomie', $result[0]->name);
        $this->assertEquals(25, $result[0]->quantity);

        $this->assertTrue(Cache::has('report:stocks'));
    }

    public function test_can_get_today_sales_and_cache_it(): void
    {
        Cache::flush();

        $product = Product::factory()->create();

        $sale = Sale::factory()->create([
            'total' => 50000,
            'created_at' => now(),
        ]);

        SaleItem::factory()->create([
            'sale_id' => $sale->id,
            'product_id' => $product->id,
            'quantity' => 5,
            'price' => 10000,
            'subtotal' => 50000,
        ]);

        $result = app(ReportService::class)->todaySales();

        $this->assertCount(1, $result);
        $this->assertEquals(50000, $result[0]->total);

        $this->assertTrue(Cache::has('report:sales:today'));
    }
}
