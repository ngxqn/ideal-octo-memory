<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Order;
use App\Models\InventoryLog;
use App\Repositories\Interfaces\InventoryLogRepositoryInterface;
use App\Repositories\Interfaces\ProductRepositoryInterface;
use App\Repositories\Interfaces\OrderRepositoryInterface;
use App\Repositories\Interfaces\OrderDetailRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Exception;

class InventoryService
{
    protected InventoryLogRepositoryInterface $inventoryLogRepo;
    protected ProductRepositoryInterface $productRepo;
    protected OrderRepositoryInterface $orderRepo;
    protected OrderDetailRepositoryInterface $orderDetailRepo;

    public function __construct(
        InventoryLogRepositoryInterface $inventoryLogRepo,
        ProductRepositoryInterface $productRepo,
        OrderRepositoryInterface $orderRepo,
        OrderDetailRepositoryInterface $orderDetailRepo
    ) {
        $this->inventoryLogRepo = $inventoryLogRepo;
        $this->productRepo = $productRepo;
        $this->orderRepo = $orderRepo;
        $this->orderDetailRepo = $orderDetailRepo;
    }

    /**
     * Tự động tạo Product (với stock = 0, base_price = 0 theo luật WAC First Inbound).
     *
     * @param array $data Dữ liệu khởi tạo sản phẩm
     * @return Product
     * @throws Exception
     */
    public function createProduct(array $data): Product
    {
        return DB::transaction(function () use ($data) {
            // Đảm bảo sản phẩm sinh ra luôn trắng tinh theo chuẩn tài chính
            $data['base_price'] = 0;
            $data['stock_quantity'] = 0;

            $product = $this->productRepo->create($data);

            if (!$product || !$product->id) {
                throw new Exception("Lỗi: Không thể khởi tạo sản phẩm mới qua Repository.");
            }

            return $product;
        });
    }

    /**
     * Đồng bộ UPDATE stock_quantity VÀ INSERT inventory_logs trong cùng 1 transaction.
     *
     * @param int $productId
     * @param int $delta Số lượng thay đổi (dương là cộng, âm là trừ)
     * @param string $refType Loại sự kiện (order_placed, order_cancelled, goods_receipt)
     * @param int $refId ID đơn hàng/phiếu nhập tham chiếu
     * @param float $unitPrice Lịch sử đơn giá (Giá nhận WAC lúc nhập, hoặc giá vốn lúc xuất)
     * @return void
     * @throws Exception
     */
    public function adjustStock(int $productId, int $delta, string $refType, int $refId, float $unitPrice = 0.00): void
    {
        if ($delta === 0) {
            return;
        }

        DB::transaction(function () use ($productId, $delta, $refType, $refId, $unitPrice) {
            // Bước 1: Trừ trực tiếp vào products.stock_quantity (CHECK constraint sẽ chặn nếu < 0)
            $this->productRepo->adjustStock($productId, $delta);

            // Bước 2: Sinh thêm một dòng inventory_logs mới để giữ chỗ với snapshot unit price
            $this->inventoryLogRepo->createEntry([
                'product_id' => $productId,
                'change_amount' => $delta,
                'unit_price' => $unitPrice,
                'reference_type' => $refType,
                'reference_id' => $refId,
            ]);
        });
    }

    /**
     * Khi đơn bị hủy, hệ thống tự động cộng trả lại vào products.stock_quantity.
     *
     * @param Order $order
     * @return void
     * @throws Exception
     */
    public function restock(Order $order): void
    {
        DB::transaction(function () use ($order) {
            // Bước 1: Lock row với SELECT ... FOR UPDATE để tránh race condition
            $lockedOrder = $this->orderRepo->findByIdWithLock($order->id);

            // Bước 2: Kiểm tra orders.status còn trong ['pending', 'confirmed']
            if (!in_array($lockedOrder->status, ['pending', 'confirmed'])) {
                throw new Exception("Không thể xử lý hoàn kho do trạng thái đơn hàng hiện tại là: " . $lockedOrder->status);
            }

            // Bước 3: Thu hồi số lượng và ghi sự kiện hoàn kho
            $details = $this->orderDetailRepo->findByOrderId($order->id);
            foreach ($details as $detail) {
                // Hủy đơn thì cộng lại (change_amount dương)
                $this->adjustStock(
                    $detail->product_id, 
                    $detail->quantity, 
                    'order_cancelled', 
                    $order->id,
                    $detail->unit_price
                );
            }
            
            // LƯU Ý: Ở đây ta chỉ cập nhật DB status thông qua Repo nếu được phép.
            // Nếu OrderService gọi restock() xong rồi mới đổi status, thì restock() này cần đảm bảo status được cập nhật
            // Cập nhật thành 'cancelled' ở đây để tránh restock kép
            $this->orderRepo->updateStatus($order->id, 'cancelled');
        });
    }
}
