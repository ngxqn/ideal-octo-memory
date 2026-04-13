<?php

namespace App\Repositories;

use App\Models\CartItem;
use App\Repositories\Interfaces\CartItemRepositoryInterface;

class CartItemRepository implements CartItemRepositoryInterface
{
    public function updateOrCreate(array $attributes, array $values = []): CartItem
    {
        return CartItem::updateOrCreate($attributes, $values);
    }

    public function update(int $id, array $data): bool
    {
        $item = CartItem::find($id);
        if ($item) {
            return $item->update($data);
        }
        return false;
    }

    public function delete(int $id): bool
    {
        return CartItem::destroy($id);
    }

    public function find(int $id): ?CartItem
    {
        return CartItem::find($id);
    }
}
