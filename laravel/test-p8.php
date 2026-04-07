<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$svc = app(\App\Services\InventoryService::class);

$cat = \App\Models\Category::firstOrCreate(['name' => 'Test Cat']);

try {
    $svc->createProduct(['category_id' => $cat->id, 'sku' => 'INIT-'.time(), 'name' => 'Init Test', 'stock_quantity' => 50]);
    echo "Success! Log count: " . \App\Models\InventoryLog::count() . "\n";
} catch (\Exception $e) {
    echo "Failed: $e\n";
}
