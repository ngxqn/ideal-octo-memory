<?php

namespace App\Repositories\Interfaces;

use App\Models\CartItem;

interface CartItemRepositoryInterface
{
    /**
     * Tìm hoặc tạo mới một item trong giỏ hàng.
     */
    public function updateOrCreate(array $attributes, array $values = []): CartItem;

    /**
     * Cập nhật số lượng của một item.
     */
    public function update(int $id, array $data): bool;

    /**
     * Xóa một item khỏi giỏ hàng.
     */
    public function delete(int $id): bool;

    /**
     * Tìm item theo ID.
     */
    public function find(int $id): ?CartItem;
}
