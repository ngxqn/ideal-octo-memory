<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('goods_receipt_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('goods_receipt_id')->constrained('goods_receipts')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnUpdate()->restrictOnDelete();
            $table->unsignedInteger('quantity');
            $table->decimal('import_price', 15, 2);
            $table->timestamp('created_at')->useCurrent();
        });

        // Add CHECK constraints
        \Illuminate\Support\Facades\DB::statement("
            ALTER TABLE goods_receipt_details 
            ADD CONSTRAINT chk_grd_qty CHECK (quantity >= 1),
            ADD CONSTRAINT chk_grd_price CHECK (import_price > 0)
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('goods_receipt_details');
    }
};
