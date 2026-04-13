<?php

namespace App\Repositories;

use App\Models\Order;
use App\Repositories\Interfaces\OrderRepositoryInterface;

class OrderRepository implements OrderRepositoryInterface
{
    public function findById(int $id): ?Order
    {
        return Order::where('id', $id)->first();
    }

    public function findByIdWithLock(int $id): ?Order
    {
        return Order::where('id', $id)->lockForUpdate()->first();
    }

    public function updateStatus(int $id, string $status): bool
    {
        return Order::where('id', $id)->update(['status' => $status]) > 0;
    }

    public function create(array $data): Order
    {
        return Order::create($data);
    }

    public function findByUser(int $userId): \Illuminate\Database\Eloquent\Collection
    {
        return Order::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
