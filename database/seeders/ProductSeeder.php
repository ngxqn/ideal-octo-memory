<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use App\Services\InventoryService;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    protected InventoryService $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Ensure Target Storage Directory exists
        if (!Storage::disk('public')->exists('products')) {
            Storage::disk('public')->makeDirectory('products');
        }

        // 2. Hydrate Default Image if it exists in Source
        $defaultSource = public_path('assets/image/seeders/products/default.png');
        if (File::exists($defaultSource) && !Storage::disk('public')->exists('products/default.png')) {
            File::copy($defaultSource, storage_path('app/public/products/default.png'));
        }

        $products = [
            [
                'category_slug' => 'ngot',
                'sku' => 'SP001',
                'name' => 'Bánh Trung thu Golden Plum',
                'description' => 'Tinh tế và mới mẻ, <strong>bánh Trung thu Golden Plum</strong> là sự kết hợp hài hòa giữa vị ngọt của mận vàng và lớp vỏ bánh nướng thơm mềm. Mang đến trải nghiệm vị giác quen mà lạ.',
                'image' => 'products/banh-trung-thu-golden-plum.png',
                'base_price' => 120000,
                'profit_margin' => 57.5,
                'stock_quantity' => 200,
                'supplier' => 'Nhà cung cấp Kirin',
            ],
            [
                'category_slug' => 'man',
                'sku' => 'SP002',
                'name' => 'Bánh Trung thu Hotate XO Mixed Nuts',
                'description' => 'Phá cách độc đáo, <strong>bánh Hotate XO Mixed Nuts</strong> mang vị mận đậm đà của sốt sò điệp XO cay nhẹ cùng sự bùi béo của các loại hạt dinh dưỡng.',
                'image' => 'products/banh-trung-thu-hotate-xo-mixed-nuts.png',
                'base_price' => 90000,
                'profit_margin' => 54.4,
                'stock_quantity' => 200,
                'supplier' => 'Hải sản Biển Đông',
            ],
            [
                'category_slug' => 'ngot',
                'sku' => 'SP003',
                'name' => 'Bánh Trung thu Matcha',
                'description' => '<strong>Bánh Trung thu Matcha</strong> mang đậm hương vị thanh mát của trà xanh Nhật Bản. Hương thơm nhẹ, vị ngọt dịu hòa quyện matcha tinh tế.',
                'image' => 'products/banh-trung-thu-matcha.png',
                'base_price' => 65000,
                'profit_margin' => 52.3,
                'stock_quantity' => 200,
                'supplier' => 'Trà xanh Uji',
            ],
            [
                'category_slug' => 'ngot',
                'sku' => 'SP004',
                'name' => 'Bánh Trung thu Murasaki Imo',
                'description' => '<strong>Bánh Trung thu Murasaki Imo</strong> mang sắc tím dịu dàng của khoai lang Nhật Bản. Nhân khoai dẻo mịn, thơm nhẹ vị ngọt tự nhiên.',
                'image' => 'products/banh-trung-thu-murasaki-imo.png',
                'base_price' => 75000,
                'profit_margin' => 53.3,
                'stock_quantity' => 200,
                'supplier' => 'Nông trại Đà Lạt',
            ],
            [
                'category_slug' => 'chay',
                'sku' => 'SP005',
                'name' => 'Bánh Trung thu Mushroom Mixed Nuts',
                'description' => 'Lựa chọn thanh đạm và dinh dưỡng cho người ăn chay. Hương thơm của nấm hòa quyện cùng vị bùi béo của các loại hạt.',
                'image' => 'products/banh-trung-thu-mushroom-mixed-nuts.png',
                'base_price' => 85000,
                'profit_margin' => 51.7,
                'stock_quantity' => 200,
                'supplier' => 'Nấm sạch Việt',
            ],
            [
                'category_slug' => 'ngot',
                'sku' => 'SP006',
                'name' => 'Bánh Trung thu Hạt Sen Dừa Non',
                'description' => 'Vị ngọt thanh, bùi béo của hạt sen hòa cùng mùi thơm nhẹ của dừa non, tạo cảm giác mềm mịn dễ chịu.',
                'image' => 'products/banh-trung-thu-pink-nocturne.png',
                'base_price' => 70000,
                'profit_margin' => 50,
                'stock_quantity' => 200,
                'supplier' => 'Nhà cung cấp Kirin',
            ],
            [
                'category_slug' => 'man',
                'sku' => 'SP007',
                'name' => 'Bánh Trung thu Đậu Đỏ Trứng Muối',
                'description' => 'Vị mặn ngọt hài hòa, nhân đậu đỏ bùi quyện cùng trứng muối tan chảy, trọn vị truyền thống và hiện đại.',
                'image' => 'products/banh-trung-thu-takesumi-orange.png',
                'base_price' => 80000,
                'profit_margin' => 56.2,
                'stock_quantity' => 0,
                'supplier' => 'Nhà cung cấp Kirin',
            ],
            [
                'category_slug' => 'ngot',
                'sku' => 'SP008',
                'name' => 'Bánh Trung thu Xôi Gấc',
                'description' => 'Màu đỏ tự nhiên từ gấc, tượng trưng cho may mắn. Hương thơm của nếp quyện cùng vị ngọt nhẹ của đậu xanh và dừa.',
                'image' => 'products/banh-trung-thu-xoi-gac.png',
                'base_price' => 65000,
                'profit_margin' => 46.1,
                'stock_quantity' => 200,
                'supplier' => 'Nông trại Đà Lạt',
            ]
        ];

        foreach ($products as $p) {
            $category = Category::where('slug', $p['category_slug'])->first();
            
            if ($category) {
                // 3. Hydrate Specific Product Image
                $sourcePath = public_path('assets/image/seeders/' . $p['image']);
                $targetPath = storage_path('app/public/' . $p['image']);
                
                if (File::exists($sourcePath) && !Storage::disk('public')->exists($p['image'])) {
                    File::copy($sourcePath, $targetPath);
                }

                // Prepare data for InventoryService
                $data = $p;
                $data['category_id'] = $category->id;
                // Chặn không đẩy thẳng Gía/Tồn vào Model nữa
                $initialBasePrice = $data['base_price'];
                $initialStock = $data['stock_quantity'];
                unset($data['category_slug'], $data['base_price'], $data['stock_quantity']);

                $product = Product::where('sku', $p['sku'])->first();
                if ($product) {
                    $product->update($data);
                } else {
                    $product = $this->inventoryService->createProduct($data);
                }

                // Thực hiện giả lập Phiếu Nhập để nâng WAC và Tồn kho hợp lệ nếu chưa có
                if ($initialStock > 0 && $product->stock_quantity == 0) {
                    $this->simulateGoodsReceipt($product, $initialStock, $initialBasePrice);
                }
            }
        }
    }

    /**
     * Helper phụ trợ: Tạo Goods Receipt nháp để mồi dữ liệu WAC và Ledger
     * đúng theo Pipeline thay vì sửa thẳng cột.
     */
    private function simulateGoodsReceipt(Product $product, int $quantity, float $importPrice)
    {
        // 1. Tạo phiếu nhập
        $receiptId = DB::table('goods_receipts')->insertGetId([
            'created_by' => 1, // Assume Admin ID = 1
            'status' => 'completed',
            'note' => 'Initial WAC Seeding',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 2. Tạo chi tiết phiếu nhập
        DB::table('goods_receipt_details')->insert([
            'goods_receipt_id' => $receiptId,
            'product_id' => $product->id,
            'quantity' => $quantity,
            'import_price' => $importPrice,
            'created_at' => now(),
        ]);

        // 3. Cập nhật base_price vào Products (WAC = Mới * do trước đó đang là 0)
        // new_base_price = ((0 * 0) + ($quantity * $importPrice)) / (0 + $quantity) = $importPrice
        $product->update([
            'base_price' => $importPrice
        ]);

        // 4. Update ledger và cache thông qua Service
        $this->inventoryService->adjustStock(
            $product->id,
            $quantity,
            'goods_receipt',
            $receiptId,
            $importPrice
        );
    }
}
