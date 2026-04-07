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
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
