<?php

namespace App\Repositories\Interfaces;

use App\Models\InventoryLog;

interface InventoryLogRepositoryInterface
{
    /**
     * Create a new inventory log entry.
     *
     * @param array $data Expected keys: product_id, change_amount, reference_type, reference_id
     * @return InventoryLog The newly created inventory log instance
     */
    public function createEntry(array $data): InventoryLog;

    /**
     * Calculate the net total of all inventory changes for a specific product.
     *
     * @param int $productId The ID of the product
     * @return int Total sum of inventory changes
     */
    public function sumByProduct(int $productId): int;
}
