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
        $catNgot = Category::where('name', 'Bánh Ngọt')->first()->id;
        $catMan = Category::where('name', 'Bánh Mặn')->first()->id;
        $catChay = Category::where('name', 'Bánh Chay')->first()->id;

        $products = [
            [
                'category_id' => $catNgot,
                'sku' => 'BN001',
                'name' => 'Bánh Golden Plum',
                'description' => 'Nhân mận vàng thượng hạng.',
                'image' => 'assets/image/product/banh-trung-thu-golden-plum.png',
                'base_price' => 150000,
                'profit_margin' => 20,
                'stock_quantity' => 50,
            ],
            [
                'category_id' => $catMan,
                'sku' => 'BM001',
                'name' => 'Bánh Hotate XO',
                'description' => 'Nhân điệp sốt XO đặc biệt.',
                'image' => 'assets/image/product/banh-trung-thu-hotate-xo-mixed-nuts.png',
                'base_price' => 180000,
                'profit_margin' => 25,
                'stock_quantity' => 30,
            ],
            [
                'category_id' => $catNgot,
                'sku' => 'BN002',
                'name' => 'Bánh Matcha',
                'description' => 'Matcha đậm đà từ Nhật Bản.',
                'image' => 'assets/image/product/banh-trung-thu-matcha.png',
                'base_price' => 140000,
                'profit_margin' => 15,
                'stock_quantity' => 100,
            ],
            [
                'category_id' => $catChay,
                'sku' => 'BC001',
                'name' => 'Bánh Mushroom Mixed Nuts',
                'description' => 'Nhân nấm và hạt dinh dưỡng cho người ăn chay.',
                'image' => 'assets/image/product/banh-trung-thu-mushroom-mixed-nuts.png',
                'base_price' => 160000,
                'profit_margin' => 20,
                'stock_quantity' => 20,
            ],
        ];

        foreach ($products as $p) {
            // Using InventoryService to ensure "Init Log" is created
            if (!Product::where('sku', $p['sku'])->exists()) {
                $this->inventoryService->createProduct($p);
            }
        }
    }
}
