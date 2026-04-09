<?php

namespace App\Services;

use App\Models\User;
use App\Models\Cart;
use App\Models\CartItem;
use App\Repositories\Interfaces\CartRepositoryInterface;
use App\Repositories\Interfaces\CartItemRepositoryInterface;
use App\Repositories\Interfaces\ProductRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Exception;

class CartService
{
    protected CartRepositoryInterface $cartRepo;
    protected CartItemRepositoryInterface $cartItemRepo;
    protected ProductRepositoryInterface $productRepo;

    public function __construct(
        CartRepositoryInterface $cartRepo,
        CartItemRepositoryInterface $cartItemRepo,
        ProductRepositoryInterface $productRepo
    ) {
        $this->cartRepo = $cartRepo;
        $this->cartItemRepo = $cartItemRepo;
        $this->productRepo = $productRepo;
    }

    /**
     * Lấy hoặc tạo giỏ hàng cho người dùng.
     */
    public function getCartForUser(User $user): Cart
    {
        return $this->cartRepo->findOrCreateByUserId($user->id);
    }

    /**
     * Thêm sản phẩm vào giỏ hàng.
     */
    public function addToCart(User $user, int $productId, int $quantity = 1): CartItem
    {
        return DB::transaction(function () use ($user, $productId, $quantity) {
            $cart = $this->getCartForUser($user);
            $product = $this->productRepo->find($productId);

            if (!$product || $product->is_deleted) {
                throw new Exception("Sản phẩm không tồn tại hoặc đã bị xóa.");
            }

            if ($product->stock_quantity < $quantity) {
                throw new Exception("Sản phẩm '{$product->name}' không đủ số lượng trong kho.");
            }

            // Tìm xem sản phẩm đã có trong giỏ chưa
            $cartItem = $this->cartItemRepo->updateOrCreate(
                [
                    'cart_id' => $cart->id,
                    'product_id' => $productId,
                ],
                // Nếu đã có thì cộng dồn, nếu chưa thì lấy quantity mới
                []
            );

            if (!$cartItem->wasRecentlyCreated) {
                $newQuantity = $cartItem->quantity + $quantity;
                
                // Kiểm tra tồn kho lần nữa với tổng số lượng mới
                if ($product->stock_quantity < $newQuantity) {
                    throw new Exception("Tổng số lượng trong giỏ hàng vượt quá tồn kho hiện có.");
                }

                $this->cartItemRepo->update($cartItem->id, ['quantity' => $newQuantity]);
                $cartItem->refresh();
            } else {
                $this->cartItemRepo->update($cartItem->id, ['quantity' => $quantity]);
                $cartItem->refresh();
            }

            return $cartItem;
        });
    }

    /**
     * Cập nhật số lượng sản phẩm trong giỏ.
     */
    public function updateQuantity(int $cartItemId, int $quantity): bool
    {
        $item = $this->cartItemRepo->find($cartItemId);
        if (!$item) return false;

        $product = $this->productRepo->find($item->product_id);
        if ($product->stock_quantity < $quantity) {
            throw new Exception("Số lượng yêu cầu vượt quá tồn kho.");
        }

        return $this->cartItemRepo->update($cartItemId, ['quantity' => $quantity]);
    }

    /**
     * Xóa sản phẩm khỏi giỏ hàng.
     */
    public function removeFromCart(int $cartItemId): bool
    {
        return $this->cartItemRepo->delete($cartItemId);
    }

    /**
     * Tính tổng giá trị giỏ hàng.
     */
    public function getCartTotal(Cart $cart): float
    {
        return (float) $cart->cartItems->reduce(function ($carry, $item) {
            return $carry + ($item->product->sell_price * $item->quantity);
        }, 0);
    }
}
