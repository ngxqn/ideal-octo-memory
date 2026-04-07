<?php

namespace App\Repositories;

use App\Models\Product;
use App\Repositories\Interfaces\ProductRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class ProductRepository implements ProductRepositoryInterface
{
    /**
     * Retrieve all active products (is_deleted = 0) using the scopeActive.
     *
     * @return Collection
     */
    public function findActive(): Collection
    {
        return Product::active()->get();
    }

    /**
     * Find a product by ID and apply a pessimistic lock (SELECT FOR UPDATE).
     * This is crucial to prevent race conditions during inventory updates.
     *
     * @param int $id The ID of the product
     * @return Product|null The product model or null if not found
     */
    public function findWithStock(int $id): ?Product
    {
        return Product::where('id', $id)->lockForUpdate()->first();
    }

    /**
     * Decrement the stock quantity of a product by a specific amount directly via DB query.
     * Note: DB constraints will fail and throw an exception if stock goes below 0.
     *
     * @param int $id The ID of the product
     * @param int $amount The amount to decrement
     * @return int Number of affected rows
     */
    public function decrementStock(int $id, int $amount): int
    {
        return Product::where('id', $id)->decrement('stock_quantity', $amount);
    }
}
