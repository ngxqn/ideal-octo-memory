<?php

namespace App\Services;

use App\Models\Product;
use App\Models\InventoryLog;
use App\Repositories\Interfaces\InventoryLogRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Exception;

class InventoryService
{
    protected InventoryLogRepositoryInterface $inventoryLogRepo;

    public function __construct(InventoryLogRepositoryInterface $inventoryLogRepo)
    {
        $this->inventoryLogRepo = $inventoryLogRepo;
    }

    /**
     * Tự động tạo Product và lưu "Init Log" vào Inventory Ledger (nếu stock > 0).
     * Bọc trong DB::transaction để bảo toàn ACID (Rule 2.2).
     *
     * @param array $data Dữ liệu khởi tạo sản phẩm
     * @return Product
     * @throws Exception
     */
    public function createProduct(array $data): Product
    {
        return DB::transaction(function () use ($data) {
            // Bước 1: Khai sinh sản phẩm. Eloquent sẽ tự throw QueryException nếu thất bại do Mass Assignment hoặc Schema constraint.
            // Nếu muốn ném Custom Exception, ta có thể tự bắt, nhưng mặc nhiên nếu throw, transaction sẽ rollback.
            $product = Product::create($data);

            if (!$product || !$product->id) {
                throw new Exception("Lỗi: Không thể khởi tạo sản phẩm mới.");
            }

            $initialStock = (int) ($data['stock_quantity'] ?? 0);

            // Bước 2: Bắt buộc tuân thủ Rule 2.2 "Init Log", tuy nhiên bảng inventory_logs có CHECK (change_amount != 0).
            // Do đó chúng ta chỉ log vào sổ cái nếu số lượng ban đầu thực sự lớn hơn 0.
            if ($initialStock > 0) {
                $this->inventoryLogRepo->createEntry([
                    'product_id' => $product->id,
                    'change_amount' => $initialStock,
                    'reference_type' => 'product_init',
                    'reference_id' => $product->id, // Dùng chính ID sản phẩm làm tham chiếu
                ]);
            }

            return $product;
        });
    }
}
