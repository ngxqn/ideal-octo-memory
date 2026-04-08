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

        // Ensure cart exists
        $cart = Cart::firstOrCreate(['user_id' => $customer->id]);

        // 1. Create a sample order for Customer
        $product = Product::where('sku', 'SP001')->first();
        if ($product) {
            // Manually add to cart to simulate placeOrder requirements
            CartItem::updateOrCreate(
                ['cart_id' => $cart->id, 'product_id' => $product->id],
                ['quantity' => 2]
            );

            $this->orderService->placeOrder($customer, $cart, [
                'receiver_name' => 'Nguyễn Văn Khách',
                'receiver_phone' => '0911222333',
                'shipping_address' => '123 Đường ABC',
                'shipping_commune' => 'Phường 5',
                'shipping_city' => 'Quận 3, TP.HCM',
                'payment_method' => 'cod',
                'note' => 'Giao giờ hành chính',
            ]);
        }
    }
}
