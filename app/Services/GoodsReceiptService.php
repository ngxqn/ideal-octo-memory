<?php

namespace App\Services;

use App\Models\User;
use App\Models\GoodsReceipt;
use App\Models\Product;
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
     * Create a new Goods Receipt (Draft or Completed).
     * Only adjusts stock if status is 'completed'.
     */
    public function createReceipt(User $admin, array $receiptData, array $itemsData): GoodsReceipt
    {
        return DB::transaction(function () use ($admin, $receiptData, $itemsData) {
            if ($admin->role !== 'admin') {
                throw new Exception("Từ chối: Chỉ quản trị viên mới được phép tạo phiếu nhập hàng.");
            }

            if (empty($itemsData)) {
                throw new Exception("Phiếu nhập hàng phải có ít nhất một sản phẩm.");
            }

            $receiptData['created_by'] = $admin->id;
            $status = $receiptData['status'] ?? 'draft';
            // Force status to draft to ensure we use completeReceipt for completed ones, 
            // but if they submit completed immediately, we handle that correctly later
            $receiptData['status'] = 'draft'; 
            
            $receipt = $this->goodsReceiptRepo->create($receiptData);

            foreach ($itemsData as $item) {
                $this->goodsReceiptDetailRepo->create([
                    'goods_receipt_id' => $receipt->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'import_price' => $item['import_price'],
                ]);
            }

            if ($status === 'completed') {
                // If it was meant to be completed right away, call completeReceipt
                return $this->completeReceipt($receipt->id);
            }

            return $receipt;
        });
    }

    /**
     * Update an existing Draft receipt.
     */
    public function updateReceipt(int $receiptId, array $receiptData, array $itemsData): GoodsReceipt
    {
        return DB::transaction(function () use ($receiptId, $receiptData, $itemsData) {
            $receipt = GoodsReceipt::lockForUpdate()->findOrFail($receiptId);

            if ($receipt->status === 'completed') {
                throw new Exception("Phiếu nhập đã hoàn thành không thể chỉnh sửa.");
            }

            // Status requested
            $status = $receiptData['status'] ?? 'draft';
            $receiptData['status'] = 'draft'; 

            $receipt->update($receiptData);

            // Resync items (delete old, insert new)
            $receipt->details()->delete();

            foreach ($itemsData as $item) {
                $this->goodsReceiptDetailRepo->create([
                    'goods_receipt_id' => $receipt->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'import_price' => $item['import_price'],
                ]);
            }

            if ($status === 'completed') {
                return $this->completeReceipt($receipt->id);
            }

            return $receipt;
        });
    }

    /**
     * Transition a 'draft' receipt to 'completed'. Execute WAC calculations and inventory ledger inserts.
     */
    public function completeReceipt(int $receiptId): GoodsReceipt
    {
        return DB::transaction(function () use ($receiptId) {
            $receipt = GoodsReceipt::with('details')->lockForUpdate()->findOrFail($receiptId);

            if ($receipt->status === 'completed') {
                throw new Exception("Phiếu nhập này đã được hoàn thành trước đó.");
            }

            $receipt->update(['status' => 'completed']);

            foreach ($receipt->details as $item) {
                // Fetch product with lock
                $product = Product::lockForUpdate()->findOrFail($item->product_id);

                $currentStock = $product->stock_quantity;
                $currentBasePrice = $product->base_price;
                
                $addedQty = $item->quantity;
                $importPrice = $item->import_price;

                $totalNewStock = $currentStock + $addedQty;

                // WAC Calculation: New Base Price = ((Current Stock * Current Base Price) + (Added Qty * Import Price)) / (Total New Stock)
                $newBasePrice = 0;
                if ($totalNewStock > 0) {
                    $newBasePrice = (($currentStock * $currentBasePrice) + ($addedQty * $importPrice)) / $totalNewStock;
                }

                // Call InventoryService (will write to inventory_logs and update stock_quantity)
                $this->inventoryService->adjustStock(
                    $product->id,
                    $addedQty,
                    'goods_receipt',
                    $receipt->id,
                    $importPrice
                );

                // Update product base price
                $product->update([
                    'base_price' => round($newBasePrice)
                ]);
            }

            return $receipt;
        });
    }

    /**
     * Delete a 'draft' receipt.
     */
    public function deleteReceipt(int $receiptId): void
    {
        DB::transaction(function () use ($receiptId) {
            $receipt = GoodsReceipt::lockForUpdate()->findOrFail($receiptId);

            if ($receipt->status === 'completed') {
                throw new Exception("Không thể xóa phiếu nhập đã hoàn thành để đảm bảo tính toàn vẹn kho.");
            }

            // Delete details first
            $receipt->details()->delete();
            
            // Delete the receipt
            $receipt->delete();
        });
    }
}
