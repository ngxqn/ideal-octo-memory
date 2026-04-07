<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Setup test state
App\Models\User::unguard();
App\Models\Category::unguard();
App\Models\Product::unguard();
App\Models\Order::unguard();
App\Models\OrderDetail::unguard();

$user = App\Models\User::firstOrCreate(
    ['email' => 'test@test.com'],
    [
        'username' => 'test', 'password' => bcrypt('123'), 'full_name' => 'T',
        'phone' => '123', 'address' => 'A', 'commune' => 'A', 'city' => 'A', 'role' => 'customer'
    ]
);

$cat = App\Models\Category::firstOrCreate(['name' => 'Restock Cat']);

$product = App\Models\Product::firstOrCreate(
    ['sku' => 'RESTOCK-999'],
    ['category_id' => $cat->id, 'name' => 'Restock', 'stock_quantity' => 10]
);

$order = App\Models\Order::create([
    'user_id' => $user->id,
    'receiver_name' => 'R', 'receiver_phone' => '1', 'shipping_address' => 'A',
    'shipping_commune' => 'A', 'shipping_city' => 'A', 'payment_method' => 'cod',
    'status' => 'pending', 'total_amount' => 100
]);

App\Models\OrderDetail::create([
    'order_id' => $order->id, 'product_id' => $product->id,
    'product_name' => 'Restock', 'unit_price' => 10, 'quantity' => 2, 'subtotal' => 20
]);

$svc = app(\App\Services\InventoryService::class);

echo "Log count before: " . \App\Models\InventoryLog::count() . "\n";
echo "Stock before: " . $product->refresh()->stock_quantity . "\n";

echo "\n--- First Restock ---\n";
try {
    $svc->restock($order);
    echo "First Restock Success.\n";
} catch (\Exception $e) {
    echo "Failed: {$e->getMessage()}\n";
}

echo "Stock after restock 1: " . $product->refresh()->stock_quantity . "\n";

echo "\n--- Second Restock ---\n";
try {
    $svc->restock($order);
    echo "Second Restock Success (Should not happen).\n";
} catch (\Exception $e) {
    echo "Double Restock Caught Expected Exception: {$e->getMessage()}\n";
}

echo "Stock after round 2: " . $product->refresh()->stock_quantity . "\n";
