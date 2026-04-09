<?php

namespace App\Repositories;

use App\Models\Cart;
use App\Repositories\Interfaces\CartRepositoryInterface;

class CartRepository implements CartRepositoryInterface
{
    public function findByUserId(int $userId): ?Cart
    {
        return Cart::where('user_id', $userId)->first();
    }

    public function create(array $data): Cart
    {
        return Cart::create($data);
    }

    public function findOrCreateByUserId(int $userId): Cart
    {
        return Cart::firstOrCreate(['user_id' => $userId]);
    }
}
