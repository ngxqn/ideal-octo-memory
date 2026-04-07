<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

App\Models\User::unguard();
App\Models\Category::unguard();
App\Models\Product::unguard();
App\Models\GoodsReceipt::unguard();
App\Models\GoodsReceiptDetail::unguard();

$admin = App\Models\User::firstOrCreate(['email' => 'admin_gr@a.com'], ['username' => 'agr', 'role' => 'admin', 'password' => '1', 'full_name' => '1', 'phone' => '1', 'address' => '1', 'commune' => '1','city' => '1']);
$customer = App\Models\User::firstOrCreate(['email' => 'customer_gr@a.com'], ['username' => 'cgr', 'role' => 'customer', 'password' => '1', 'full_name' => '1', 'phone' => '1', 'address' => '1', 'commune' => '1','city' => '1']);

$cat = App\Models\Category::firstOrCreate(['name' => 'Cat12']);
$product = App\Models\Product::firstOrCreate(
    ['sku' => 'P12'],
    ['category_id' => $cat->id, 'name' => 'P12', 'base_price' => 10, 'profit_margin' => 10, 'stock_quantity' => 10]
);

$svc = app(\App\Services\GoodsReceiptService::class);

echo "Initial Stock: {$product->refresh()->stock_quantity}\n";

echo "--- TRY WITH CUSTOMER ---\n";
try {
    $svc->createReceipt($customer, ['note' => 'test'], [
        ['product_id' => $product->id, 'quantity' => 5, 'import_price' => 15]
    ]);
} catch (\Exception $e) {
    echo "Caught: " . $e->getMessage() . "\n";
}

echo "\n--- TRY WITH ADMIN (SUCCESS) ---\n";
try {
    $receipt = $svc->createReceipt($admin, ['note' => 'test success'], [
        ['product_id' => $product->id, 'quantity' => 5, 'import_price' => 15]
    ]);
    echo "Receipt ID: {$receipt->id}\n";
} catch (\Exception $e) {
    echo "Failed: " . $e->getMessage() . "\n";
}

echo "Stock After: {$product->refresh()->stock_quantity}\n";
$snapshotPrice = App\Models\GoodsReceiptDetail::where('goods_receipt_id', $receipt->id)->first()->import_price;
echo "Snapshot Price: {$snapshotPrice}\n";

echo "\n--- TRY WITH ADMIN (FAIL ONE RECORD TO ROLLBACK) ---\n";
try {
    $svc->createReceipt($admin, ['note' => 'test fail'], [
        ['product_id' => -999, 'quantity' => 5, 'import_price' => 15] // Invalid product_id
    ]);
} catch (\Exception $e) {
    echo "Caught Error: " . $e->getMessage() . "\n";
}
echo "Stock Final (Should rollback): {$product->refresh()->stock_quantity}\n";
