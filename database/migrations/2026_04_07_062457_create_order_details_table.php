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
        Schema::create('order_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('product_name', 200);
            $table->decimal('unit_price', 15, 2);
            $table->unsignedInteger('quantity');
            $table->decimal('subtotal', 15, 2);
            $table->timestamp('created_at')->useCurrent();
        });

        // Add CHECK constraints
        \Illuminate\Support\Facades\DB::statement("
            ALTER TABLE order_details 
            ADD CONSTRAINT chk_order_details_qty CHECK (quantity >= 1),
            ADD CONSTRAINT chk_order_details_price CHECK (unit_price >= 0)
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_details');
    }
};
