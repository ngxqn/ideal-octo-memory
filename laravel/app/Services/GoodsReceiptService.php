<?php

namespace App\Services;

use App\Models\User;
use App\Models\GoodsReceipt;
use App\Repositories\Interfaces\GoodsReceiptRepositoryInterface;
use App\Repositories\Interfaces\GoodsReceiptDetailRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Exception;

class GoodsReceiptService
{
    protected GoodsReceiptRepositoryInterface $goodsReceiptRepo;
    protected GoodsReceiptDetailRepositoryInterface $goodsReceiptDetailRepo;
    protected InventoryService $inventoryService;

    public function __construct(
        GoodsReceiptRepositoryInterface $goodsReceiptRepo,
        GoodsReceiptDetailRepositoryInterface $goodsReceiptDetailRepo,
        InventoryService $inventoryService
    ) {
        $this->goodsReceiptRepo = $goodsReceiptRepo;
        $this->goodsReceiptDetailRepo = $goodsReceiptDetailRepo;
        $this->inventoryService = $inventoryService;
    }

    /**
     * Tạo phiếu nhập hàng để khép kín vòng đời tồn kho.
     * 
     * @param User $admin
     * @param array $receiptData
     * @param array $itemsData
     * @return GoodsReceipt
     * @throws Exception
     */
    public function createReceipt(User $admin, array $receiptData, array $itemsData): GoodsReceipt
    {
        return DB::transaction(function () use ($admin, $receiptData, $itemsData) {
            // 1. Validate quyền
            if ($admin->role !== 'admin') {
                throw new Exception("Từ chối: Chỉ quản trị viên mới được phép tạo phiếu nhập hàng.");
            }

            if (empty($itemsData)) {
                throw new Exception("Phiếu nhập hàng phải có ít nhất một sản phẩm.");
            }

            // 2. Tạo GoodsReceipt record
            $receiptData['created_by'] = $admin->id;
            $receiptData['status'] = $receiptData['status'] ?? 'completed'; // Tuỳ quy trình nghiệp vụ, tạm mặc định completed
            $receipt = $this->goodsReceiptRepo->create($receiptData);

            // 3. Loop qua $itemsData
            foreach ($itemsData as $item) {
                // Tạo GoodsReceiptDetail (snapshot import_price)
                $this->goodsReceiptDetailRepo->create([
                    'goods_receipt_id' => $receipt->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'import_price' => $item['import_price'],
                ]);

                // Gọi InventoryService::adjustStock() với delta DƯƠNG
                $this->inventoryService->adjustStock(
                    $item['product_id'],
                    $item['quantity'],
                    'goods_receipt',
                    $receipt->id
                );
            }

            return $receipt;
        });
    }
}
