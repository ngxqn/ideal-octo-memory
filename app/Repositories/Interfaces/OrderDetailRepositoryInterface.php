<?php

namespace App\Repositories\Interfaces;

use Illuminate\Database\Eloquent\Collection;

interface OrderDetailRepositoryInterface
{
    /**
     * Retrieve all order details for a given order ID.
     */
    public function findByOrderId(int $orderId): Collection;

    /**
     * Create a new order detail.
     */
    public function create(array $data): \App\Models\OrderDetail;
}
