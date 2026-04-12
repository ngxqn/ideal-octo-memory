<?php

namespace App\Services;

use App\Models\User;
use App\Models\Cart;
use App\Models\Order;
use App\Repositories\Interfaces\ProductRepositoryInterface;
use App\Repositories\Interfaces\OrderRepositoryInterface;
use App\Repositories\Interfaces\OrderDetailRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Exception;

class OrderService
{
    protected ProductRepositoryInterface $productRepo;
    protected OrderRepositoryInterface $orderRepo;
    protected OrderDetailRepositoryInterface $orderDetailRepo;
    protected InventoryService $inventoryService;

    public function __construct(
        ProductRepositoryInterface $productRepo,
        OrderRepositoryInterface $orderRepo,
        OrderDetailRepositoryInterface $orderDetailRepo,
        InventoryService $inventoryService
    ) {
        $this->productRepo = $productRepo;
        $this->orderRepo = $orderRepo;
        $this->orderDetailRepo = $orderDetailRepo;
        $this->inventoryService = $inventoryService;
    }

    /**
     * Tạo đơn hàng mới từ giỏ hàng.
     * Quy trình:
     * 1. Validate số lượng tồn kho từng item
     * 2. Tạo Order snapshot (với address)
     * 3. Loop từng item -> Tạo OrderDetail snapshot (product_name, unit_price)
     * 4. Gọi InventoryService::adjustStock để trừ kho reservation
     * 5. Xóa cart_items
     * 
     * @param User $user
     * @param Cart $cart
     * @param array $shippingData
     * @return Order
     * @throws Exception
     */
    public function placeOrder(User $user, Cart $cart, array $shippingData): Order
    {
        return DB::transaction(function () use ($user, $cart, $shippingData) {
            $cartItems = $cart->cartItems;
            
            if ($cartItems->isEmpty()) {
                throw new Exception("Giỏ hàng trống, không thể đặt hàng.");
            }

            $totalAmount = 0;
            $itemsData = [];

            // 1. Validate stock & collect snapshot data
            foreach ($cartItems as $item) {
                // Must lock row to check true stock securely
                $lockedProduct = $this->productRepo->findWithStock($item->product_id);

                if (!$lockedProduct || $lockedProduct->is_hidden || $lockedProduct->stock_quantity < $item->quantity) {
                    throw new Exception("Sản phẩm '{$lockedProduct->name}' không đủ số lượng trong kho.");
                }

                // Tính tiền dựa trên sell_price hiên hành - SNAPSHOT TRỰC TIẾP TẠI ĐÂY
                $unitPrice = $lockedProduct->sell_price;
                $subtotal = $unitPrice * $item->quantity;
                $totalAmount += $subtotal;

                $itemsData[] = [
                    'product_id' => $lockedProduct->id,
                    'product_name' => $lockedProduct->name,
                    'unit_price' => $unitPrice,
                    'quantity' => $item->quantity,
                    'subtotal' => $subtotal,
                ];
            }

            // 2. Tạo Order record
            $order = $this->orderRepo->create([
                'user_id' => $user->id,
                'receiver_name' => $shippingData['receiver_name'],
                'receiver_phone' => $shippingData['receiver_phone'],
                'shipping_address' => $shippingData['shipping_address'],
                'shipping_commune' => $shippingData['shipping_commune'],
                'shipping_city' => $shippingData['shipping_city'],
                'payment_method' => $shippingData['payment_method'],
                'status' => 'pending',
                'total_amount' => $totalAmount,
                'note' => $shippingData['note'] ?? null,
            ]);

            // 3. Tạo OrderDetail & Trừ kho
            foreach ($itemsData as $data) {
                $this->orderDetailRepo->create([
                    'order_id' => $order->id,
                    'product_id' => $data['product_id'],
                    'product_name' => $data['product_name'], // Snapshot Name
                    'unit_price' => $data['unit_price'],     // Snapshot Price
                    'quantity' => $data['quantity'],
                    'subtotal' => $data['subtotal'],
                ]);

                // Gọi tới InventoryService để xử lý Trừ Kho + Ghi Log
                $this->inventoryService->adjustStock(
                    $data['product_id'],
                    -$data['quantity'],
                    'order_placed',
                    $order->id
                );
            }

            // 4. Xóa cart_items (Dọn giỏ hàng) - thao tác xóa nhanh qua relation
            $cart->cartItems()->delete();

            return $order;
        });
    }

    /**
     * Hủy đơn hàng và tự động hoàn kho (trường hợp áp dụng Guard C1).
     * 
     * @param int $orderId
     * @param User $actor
     * @return void
     * @throws Exception
     * @throws \App\Exceptions\OrderAlreadyCancelledException
     */
    public function cancelOrder(int $orderId, User $actor): void
    {
        DB::transaction(function () use ($orderId, $actor) {
            // (1) Lock row với SELECT ... FOR UPDATE
            $order = $this->orderRepo->findByIdWithLock($orderId);

            if (!$order) {
                throw new Exception("Không tìm thấy đơn hàng để hủy.");
            }

            // Kiểm tra quyền: chỉ admin hoặc chủ đơn mới được hủy
            if ($actor->role !== 'admin' && $order->user_id !== $actor->id) {
                throw new Exception("Bạn không có quyền hủy đơn hàng này.");
            }

            // (2) Kiểm tra & (3) Ném exception nếu đã cancelled hoặc delivered
            if ($order->status === 'cancelled') {
                throw new \App\Exceptions\OrderAlreadyCancelledException("Đơn hàng số #{$orderId} đã bị hủy trước đó.");
            }
            if ($order->status === 'delivered') {
                throw new Exception("Nghiêm cấm hủy đơn: Đơn hàng số #{$orderId} đã được giao thành công.");
            }

            // (4) Gọi InventoryService để tự động quét toàn bộ order_details và hoàn lại tồn kho
            // (InventoryService::restock cũng tự động update luôn trạng thái đơn về cancelled sau khi trừ xong).
            $this->inventoryService->restock($order);
        });
    }
}
