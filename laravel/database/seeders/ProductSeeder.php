<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use App\Services\InventoryService;

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
        $products = [
            [
                'category_slug' => 'ngot',
                'sku' => 'SP001',
                'name' => 'Bánh Trung thu Golden Plum',
                'description' => 'Tinh tế và mới mẻ, <strong>bánh Trung thu Golden Plum</strong> là sự kết hợp hài hòa giữa vị ngọt của mận vàng và lớp vỏ bánh nướng thơm mềm. Mang đến trải nghiệm vị giác quen mà lạ.',
                'image' => 'assets/image/products/banh-trung-thu-golden-plum.png',
                'base_price' => 120000,
                'profit_margin' => 57.5, // (189-120)/120 = 57.5% -> sell_price 189,000
                'stock_quantity' => 25,
            ],
            [
                'category_slug' => 'man',
                'sku' => 'SP002',
                'name' => 'Bánh Trung thu Hotate XO Mixed Nuts',
                'description' => 'Phá cách độc đáo, <strong>bánh Hotate XO Mixed Nuts</strong> mang vị mặn đậm đà của sốt sò điệp XO cay nhẹ cùng sự bùi béo của các loại hạt dinh dưỡng.',
                'image' => 'assets/image/products/banh-trung-thu-hotate-xo-mixed-nuts.png',
                'base_price' => 90000,
                'profit_margin' => 54.4, // (139-90)/90 = 54.4% -> sell_price ~139,000
                'stock_quantity' => 12,
            ],
            [
                'category_slug' => 'ngot',
                'sku' => 'SP003',
                'name' => 'Bánh Trung thu Matcha',
                'description' => '<strong>Bánh Trung thu Matcha</strong> mang đậm hương vị thanh mát của trà xanh Nhật Bản. Hương thơm nhẹ, vị ngọt dịu hòa quyện matcha tinh tế.',
                'image' => 'assets/image/products/banh-trung-thu-matcha.png',
                'base_price' => 65000,
                'profit_margin' => 52.3, // (99-65)/65 = 52.3% -> sell_price ~99,000
                'stock_quantity' => 40,
            ],
            [
                'category_slug' => 'ngot',
                'sku' => 'SP004',
                'name' => 'Bánh Trung thu Murasaki Imo',
                'description' => '<strong>Bánh Trung thu Murasaki Imo</strong> mang sắc tím dịu dàng của khoai lang Nhật Bản. Nhân khoai dẻo mịn, thơm nhẹ vị ngọt tự nhiên.',
                'image' => 'assets/image/products/banh-trung-thu-murasaki-imo.png',
                'base_price' => 75000,
                'profit_margin' => 53.3, // (115-75)/75 = 53.3% -> sell_price ~115,000
                'stock_quantity' => 30,
            ],
            [
                'category_slug' => 'chay',
                'sku' => 'SP005',
                'name' => 'Bánh Trung thu Mushroom Mixed Nuts',
                'description' => 'Lựa chọn thanh đạm và dinh dưỡng cho người ăn chay. Hương thơm của nấm hòa quyện cùng vị bùi béo của các loại hạt.',
                'image' => 'assets/image/products/banh-trung-thu-mushroom-mixed-nuts.png',
                'base_price' => 85000,
                'profit_margin' => 51.7, // (129-85)/85 = 51.7% -> sell_price ~129,000
                'stock_quantity' => 18,
            ],
            [
                'category_slug' => 'ngot',
                'sku' => 'SP006',
                'name' => 'Bánh Trung thu Hạt Sen Dừa Non',
                'description' => 'Vị ngọt thanh, bùi béo của hạt sen hòa cùng mùi thơm nhẹ của dừa non, tạo cảm giác mềm mịn dễ chịu.',
                'image' => 'assets/image/products/banh-trung-thu-pink-nocturne.png',
                'base_price' => 70000,
                'profit_margin' => 50, // (105-70)/70 = 50% -> sell_price ~105,000
                'stock_quantity' => 50,
            ],
            [
                'category_slug' => 'man',
                'sku' => 'SP007',
                'name' => 'Bánh Trung thu Đậu Đỏ Trứng Muối',
                'description' => 'Vị mặn ngọt hài hòa, nhân đậu đỏ bùi quyện cùng trứng muối tan chảy, trọn vị truyền thống và hiện đại.',
                'image' => 'assets/image/products/banh-trung-thu-takesumi-orange.png',
                'base_price' => 80000,
                'profit_margin' => 56.2, // (125-80)/80 = 56.2% -> sell_price ~125,000
                'stock_quantity' => 0, // Out of stock as in original
            ],
            [
                'category_slug' => 'ngot',
                'sku' => 'SP008',
                'name' => 'Bánh Trung thu Xôi Gấc',
                'description' => 'Màu đỏ tự nhiên từ gấc, tượng trưng cho may mắn. Hương thơm của nếp quyện cùng vị ngọt nhẹ của đậu xanh và dừa.',
                'image' => 'assets/image/products/banh-trung-thu-xoi-gac.png',
                'base_price' => 65000,
                'profit_margin' => 46.1, // (95-65)/65 = 46.1% -> sell_price ~95,000
                'stock_quantity' => 35,
            ]
        ];

        foreach ($products as $p) {
            $category = Category::where('slug', $p['category_slug'])->first();
            
            if ($category) {
                // Prepare data for InventoryService
                $data = $p;
                $data['category_id'] = $category->id;
                unset($data['category_slug']);

                $product = Product::where('sku', $p['sku'])->first();
                if ($product) {
                    $product->update($data);
                } else {
                    $this->inventoryService->createProduct($data);
                }
            }
        }
    }
}
