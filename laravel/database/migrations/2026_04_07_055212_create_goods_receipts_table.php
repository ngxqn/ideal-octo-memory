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
        Schema::create('goods_receipts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('created_by')->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->enum('status', ['draft', 'completed'])->default('draft')->index('ix_goods_receipts_status');
            $table->text('note')->nullable();
            $table->timestamps();

            $table->index('created_by', 'ix_goods_receipts_created_by');
            $table->index('created_at', 'ix_goods_receipts_created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('goods_receipts');
    }
};
