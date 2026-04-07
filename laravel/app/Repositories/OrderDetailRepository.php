<?php

namespace App\Repositories;

use App\Models\OrderDetail;
use App\Repositories\Interfaces\OrderDetailRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class OrderDetailRepository implements OrderDetailRepositoryInterface
{
    public function findByOrderId(int $orderId): Collection
    {
        return OrderDetail::where('order_id', $orderId)->get();
    }
}
