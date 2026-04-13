<?php

namespace App\Repositories;

use App\Models\InventoryLog;
use App\Repositories\Interfaces\InventoryLogRepositoryInterface;

class InventoryLogRepository implements InventoryLogRepositoryInterface
{
    /**
     * Create a new inventory log entry in the database.
     *
     * @param array $data Expected keys: product_id, change_amount, reference_type, reference_id
     * @return InventoryLog The newly created inventory log instance
     */
    public function createEntry(array $data): InventoryLog
    {
        return InventoryLog::create($data);
    }

    /**
     * Calculate the net total of all inventory changes for a specific product.
     * This is useful for auditing the stock_quantity against the Ledger.
     *
     * @param int $productId The ID of the product
     * @return int Total sum of inventory changes
     */
    public function sumByProduct(int $productId): int
    {
        return (int) InventoryLog::where('product_id', $productId)->sum('change_amount');
    }
}
