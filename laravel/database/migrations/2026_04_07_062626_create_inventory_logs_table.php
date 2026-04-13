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
        Schema::create('inventory_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnUpdate()->restrictOnDelete();
            $table->integer('change_amount');
            $table->decimal('unit_price', 15, 2)->default(0.00);
            $table->enum('reference_type', ['product_init', 'goods_receipt', 'order_placed', 'order_cancelled']);
            $table->unsignedBigInteger('reference_id');
            $table->timestamp('created_at')->useCurrent();

            $table->index('product_id', 'ix_inventory_logs_product_id');
            $table->index(['product_id', 'created_at'], 'ix_inventory_logs_product_created');
            $table->index(['reference_type', 'reference_id'], 'ix_inventory_logs_reference');
            $table->index('created_at', 'ix_inventory_logs_created_at');
        });

        // Add CHECK constraint
        \Illuminate\Support\Facades\DB::statement("
            ALTER TABLE inventory_logs 
            ADD CONSTRAINT chk_inventory_change_nonzero CHECK (change_amount != 0),
            ADD CONSTRAINT chk_inventory_logs_unit_price CHECK (unit_price >= 0)
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_logs');
    }
};
