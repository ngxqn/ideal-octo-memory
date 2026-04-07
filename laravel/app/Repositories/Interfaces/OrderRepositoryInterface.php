<?php

namespace App\Repositories\Interfaces;

use App\Models\Order;

interface OrderRepositoryInterface
{
    /**
     * Find an order by ID and apply a pessimistic lock (SELECT FOR UPDATE).
     */
    public function findByIdWithLock(int $id): ?Order;

    /**
     * Update the status of an order.
     */
    public function updateStatus(int $id, string $status): bool;
}
