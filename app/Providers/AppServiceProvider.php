<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            \App\Repositories\Interfaces\ProductRepositoryInterface::class,
            \App\Repositories\ProductRepository::class
        );
        $this->app->bind(
            \App\Repositories\Interfaces\InventoryLogRepositoryInterface::class,
            \App\Repositories\InventoryLogRepository::class
        );
        $this->app->bind(
            \App\Repositories\Interfaces\OrderRepositoryInterface::class,
            \App\Repositories\OrderRepository::class
        );
        $this->app->bind(
            \App\Repositories\Interfaces\OrderDetailRepositoryInterface::class,
            \App\Repositories\OrderDetailRepository::class
        );
        $this->app->bind(
            \App\Repositories\Interfaces\GoodsReceiptRepositoryInterface::class,
            \App\Repositories\GoodsReceiptRepository::class
        );
        $this->app->bind(
            \App\Repositories\Interfaces\GoodsReceiptDetailRepositoryInterface::class,
            \App\Repositories\GoodsReceiptDetailRepository::class
        );
        $this->app->bind(
            \App\Repositories\Interfaces\CartRepositoryInterface::class,
            \App\Repositories\CartRepository::class
        );
        $this->app->bind(
            \App\Repositories\Interfaces\CartItemRepositoryInterface::class,
            \App\Repositories\CartItemRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->environment('production')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }
    }
}
