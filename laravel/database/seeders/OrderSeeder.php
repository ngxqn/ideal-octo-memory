<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Cart;
use App\Models\Product;
use App\Models\CartItem;
use App\Services\OrderService;

class OrderSeeder extends Seeder
{
    protected OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customer = User::where('role', 'customer')->first();
        if (!$customer) return;

        $products = Product::where('is_hidden', false)->get();
        if ($products->isEmpty()) return;

        // Ensure cart exists
        $cart = Cart::firstOrCreate(['user_id' => $customer->id]);

        $statuses = ['pending', 'confirmed', 'delivered', 'cancelled'];
        $cities = ['TP.HCM', 'Hà Nội', 'Đà Nẵng', 'Cần Thơ', 'Hải Phòng'];
        $communes = ['Phường 1', 'Phường Bến Nghé', 'Xã Tân Kiên', 'Phường 5', 'Quận Cầu Giấy'];

        for ($i = 1; $i <= 30; $i++) {
            // Clear previous cart items
            $cart->cartItems()->delete();

            // Add 1-3 random products to cart
            $numItems = rand(1, 3);
            $selectedProducts = $products->random(min($numItems, $products->count()));
            
            foreach ($selectedProducts as $product) {
                // Ensure stock is sufficient for seeding
                $product->stock_quantity = 999;
                $product->save();

                CartItem::create([
                    'cart_id' => $cart->id,
                    'product_id' => $product->id,
                    'quantity' => rand(1, 5)
                ]);
            }

            // Place order
            $order = $this->orderService->placeOrder($customer, $cart, [
                'receiver_name' => 'Khách hàng ' . $i,
                'receiver_phone' => '09' . rand(10000000, 99999999),
                'shipping_address' => rand(1, 500) . ' Đường ' . ['Lê Lợi', 'Nguyễn Huệ', 'Trần Hưng Đạo', 'Lý Tự Trọng'][rand(0, 3)],
                'shipping_commune' => $communes[array_rand($communes)],
                'shipping_city' => $cities[array_rand($cities)],
                'payment_method' => ['cod', 'bank_transfer', 'online'][rand(0, 2)],
                'note' => $i % 5 == 0 ? 'Giao gấp trong ngày' : null,
            ]);

            // Randomize status and date
            $order->status = $statuses[array_rand($statuses)];
            $order->created_at = now()->subDays(rand(0, 20))->subHours(rand(0, 23));
            $order->updated_at = $order->created_at->addMinutes(rand(30, 120));
            $order->save();
        }
    }
}
