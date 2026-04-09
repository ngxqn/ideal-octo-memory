<?php

namespace App\Repositories\Interfaces;

use App\Models\Cart;

interface CartRepositoryInterface
{
    /**
     * Tìm giỏ hàng theo User ID.
     */
    public function findByUserId(int $userId): ?Cart;

    /**
     * Tạo giỏ hàng mới cho người dùng.
     */
    public function create(array $data): Cart;

    /**
     * Tìm hoặc tạo mới giỏ hàng cho người dùng.
     */
    public function findOrCreateByUserId(int $userId): Cart;
}
