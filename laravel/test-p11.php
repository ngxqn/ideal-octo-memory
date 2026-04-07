<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

App\Models\User::unguard();
App\Models\Category::unguard();
App\Models\Product::unguard();
App\Models\Order::unguard();
App\Models\OrderDetail::unguard();

$admin = App\Models\User::firstOrCreate(['email' => 'admin2@a.com'], ['username' => 'a2', 'role' => 'admin', 'password' => '1', 'full_name' => '1', 'phone' => '1', 'address' => '1', 'commune' => '1','city' => '1']);
$customer = App\Models\User::firstOrCreate(['email' => 'c2@c.com'], ['username' => 'c2', 'role' => 'customer', 'password' => '1', 'full_name' => '1', 'phone' => '1', 'address' => '1', 'commune' => '1','city' => '1']);

$cat = App\Models\Category::firstOrCreate(['name' => 'Cat11']);
$product = App\Models\Product::firstOrCreate(
    ['sku' => 'P11'],
    ['category_id' => $cat->id, 'name' => 'P11', 'base_price' => 10, 'profit_margin' => 10, 'stock_quantity' => 10]
);

$order = App\Models\Order::create([
    'user_id' => $customer->id,
    'receiver_name' => 'XX', 'receiver_phone' => '1', 'shipping_address' => 'X',
    'shipping_commune' => 'Y', 'shipping_city' => 'Z', 'payment_method' => 'cod',
    'status' => 'pending', 'total_amount' => 11
]);

App\Models\OrderDetail::create([
    'order_id' => $order->id, 'product_id' => $product->id,
    'product_name' => 'P11', 'unit_price' => 11, 'quantity' => 2, 'subtotal' => 22
]);

$svc = app(\App\Services\OrderService::class);

echo "--- CANCEL 1 ---\n";
try {
    $svc->cancelOrder($order->id, $customer);
    echo "Cancel 1 Success!\n";
} catch (\Exception $e) {
    echo "Fail 1: " . get_class($e) . " - " . $e->getMessage() . "\n";
}

echo "\n--- CANCEL 2 ---\n";
try {
    $svc->cancelOrder($order->id, $admin); // Try as admin
} catch (\Exception $e) {
    echo "Fail 2: " . get_class($e) . " - " . $e->getMessage() . "\n";
}

echo "\n--- CANCEL DELIVERED ---\n";
$order2 = App\Models\Order::create([
    'user_id' => $customer->id,
    'receiver_name' => 'YY', 'receiver_phone' => '2', 'shipping_address' => 'X',
    'shipping_commune' => 'Y', 'shipping_city' => 'Z', 'payment_method' => 'cod',
    'status' => 'delivered', 'total_amount' => 11
]);

try {
    $svc->cancelOrder($order2->id, $customer);
} catch (\Exception $e) {
    echo "Fail 3: " . get_class($e) . " - " . $e->getMessage() . "\n";
}
