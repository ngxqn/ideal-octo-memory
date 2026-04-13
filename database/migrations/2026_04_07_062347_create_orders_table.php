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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('receiver_name', 100);
            $table->string('receiver_phone', 20);
            $table->string('shipping_address', 255);
            $table->string('shipping_commune', 100);
            $table->string('shipping_city', 100);
            $table->enum('payment_method', ['cod', 'bank_transfer', 'online']);
            $table->enum('status', ['pending', 'confirmed', 'delivered', 'cancelled'])->default('pending')->index('ix_orders_status');
            $table->decimal('total_amount', 15, 2);
            $table->text('note')->nullable();
            $table->timestamps();

            $table->index('user_id', 'ix_orders_user_id');
            $table->index('created_at', 'ix_orders_created_at');
            $table->index('shipping_commune', 'ix_orders_shipping_commune');
            $table->index('shipping_city', 'ix_orders_shipping_city');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
