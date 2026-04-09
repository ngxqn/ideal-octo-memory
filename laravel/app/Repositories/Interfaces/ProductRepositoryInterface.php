<?php

namespace App\Repositories\Interfaces;

use Illuminate\Database\Eloquent\Collection;
use App\Models\Product;

interface ProductRepositoryInterface
{
    /**
     * Retrieve all active products (is_deleted = 0).
     *
     * @return Collection
     */
    public function findActive(): Collection;

    /**
     * Find a product by ID.
     */
    public function find(int $id): ?Product;

    /**
     * Find a product by ID and secure the row for stock updates using pessimistic locking (forUpdate).
     * Useful when inside DB::transaction() to prevent race conditions.
     *
     * @param int $id The ID of the product
     * @return Product|null The product model or null if not found
     */
    public function findWithStock(int $id): ?Product;

    /**
     * Decrement the stock quantity of a product by a specific amount directly via DB query.
     * Note: The database CHECK constraint will cause this to fail if stock drops below 0.
     *
     * @param int $id The ID of the product
     * @param int $amount The amount to decrement
     * @return int Number of affected rows
     */
    public function decrementStock(int $id, int $amount): int;

    /**
     * Create a new product.
     *
     * @param array $data The data for the product
     * @return Product The newly created product instance
     */
    public function create(array $data): Product;

    /**
     * Adjust the stock quantity by a specific delta (positive or negative).
     */
    public function adjustStock(int $id, int $amount): int;
}
