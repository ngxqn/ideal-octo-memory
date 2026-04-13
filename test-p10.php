<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

App\Models\User::unguard();
App\Models\Category::unguard();
App\Models\Product::unguard();
App\Models\Cart::unguard();
App\Models\CartItem::unguard();

$user = App\Models\User::firstOrCreate(['email' => 't10@t.com'], ['username' => 't10', 'password' => '123', 'full_name' => '123', 'phone' => '123', 'address' => '123', 'commune'=>'123', 'city'=>'123']);
$cart = \App\Models\Cart::firstOrCreate(['user_id' => $user->id]);

$cat = \App\Models\Category::firstOrCreate(['name' => 'Cat10']);
$product = \App\Models\Product::firstOrCreate(
    ['sku' => 'P10-TEST'],
    ['category_id' => $cat->id, 'name' => 'P10', 'base_price' => 100, 'profit_margin' => 10, 'stock_quantity' => 10]
);

\App\Models\CartItem::create([
    'cart_id' => $cart->id,
    'product_id' => $product->id,
    'quantity' => 4
]);

$svc = app(\App\Services\OrderService::class);

echo "Initial Stock: {$product->refresh()->stock_quantity}\n";
echo "Cart Items Before: {$cart->cartItems()->count()}\n";

$order = $svc->placeOrder($user, $cart, [
    'receiver_name' => 'A',
    'receiver_phone' => '1',
    'shipping_address' => 'X',
    'shipping_commune' => 'Y',
    'shipping_city' => 'Z',
    'payment_method' => 'cod',
]);

echo "Order created ID: {$order->id}, Total: {$order->total_amount}\n";
echo "Cart Items After: {$cart->cartItems()->count()}\n";
echo "Stock After Order: {$product->refresh()->stock_quantity}\n";

// CHANGE PRICE! ADMIN CHANGES PRICE
$product->update(['base_price' => 200]);
echo "ADMIN CHANGES PRICE TO 200\n";
echo "New Product Sell Price: {$product->refresh()->sell_price}\n";

// DOES ORDER DETAIL CHANGE?
$snapshotPrice = \App\Models\OrderDetail::where('order_id', $order->id)->first()->unit_price;
echo "Snapshot Order Price stays: {$snapshotPrice}\n";

// TEST INSUFFICIENT STOCK
\App\Models\CartItem::create(['cart_id' => $cart->id, 'product_id' => $product->id, 'quantity' => 10]);
try {
    $svc->placeOrder($user, $cart, ['receiver_name' => 'B', 'receiver_phone' => '1', 'shipping_address' => 'X', 'shipping_commune' => 'Y', 'shipping_city' => 'Z', 'payment_method' => 'cod']);
} catch (\Exception $e) {
    echo "Caught expected insufficient stock: {$e->getMessage()}\n";
}

echo "Final Stock (Should Rollback): {$product->refresh()->stock_quantity}\n";
echo "Final Cart Items (Should not delete): {$cart->cartItems()->count()}\n";

