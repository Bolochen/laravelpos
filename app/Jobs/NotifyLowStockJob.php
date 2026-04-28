<?php

namespace App\Jobs;

use App\Models\Product;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class NotifyLowStockJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public int $productId)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $product = Product::with('stock')->find($this->productId);

        logger()->warning('LOW STOCK NOTIFICATION', [
            'product' => $product->name,
            'stock' => $product->stock->quantity ?? 0,
        ]);

        // later:
        // TelegramService::send("Stock {$product->name} below 10");
    }
}
