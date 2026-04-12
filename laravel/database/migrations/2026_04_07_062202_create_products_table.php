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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('sku', 50)->unique('uq_products_sku');
            $table->string('name', 200);
            $table->text('description')->nullable();
            $table->string('image', 500)->nullable();
            $table->string('supplier', 200)->nullable();
            $table->decimal('base_price', 15, 2)->default(0.00);
            $table->decimal('profit_margin', 5, 2)->default(0.00);
            // sell_price (GENERATED COLUMN) will be added via DB statement
            $table->integer('stock_quantity')->default(0);
            $table->integer('low_stock_threshold')->default(10);
            $table->boolean('is_hidden')->default(0)->index('ix_products_is_hidden');
            $table->timestamps();

            $table->index(['category_id', 'is_hidden'], 'ix_products_category_active');
        });

        // Add GENERATED STORED column
        \Illuminate\Support\Facades\DB::statement("
            ALTER TABLE products 
            ADD sell_price DECIMAL(15, 2) GENERATED ALWAYS AS (ROUND(base_price * (1 + profit_margin / 100), 2)) STORED 
            AFTER profit_margin
        ");

        // Add indexes for the generated column
        Schema::table('products', function (Blueprint $table) {
            $table->index('sell_price', 'ix_products_sell_price');
        });

        // Add CHECK constraint
        \Illuminate\Support\Facades\DB::statement("
            ALTER TABLE products 
            ADD CONSTRAINT chk_products_stock_non_negative CHECK (stock_quantity >= 0)
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
