<?php

namespace App\Repositories\Interfaces;

use App\Models\Order;

interface OrderRepositoryInterface
{
    /**
     * Find an order by ID.
     */
    public function findById(int $id): ?Order;

    /**
     * Find an order by ID and apply a pessimistic lock (SELECT FOR UPDATE).
     */
    public function findByIdWithLock(int $id): ?Order;

    /**
     * Update the status of an order.
     */
    public function updateStatus(int $id, string $status): bool;

    /**
     * Create a new order.
     */
    public function create(array $data): Order;
}
