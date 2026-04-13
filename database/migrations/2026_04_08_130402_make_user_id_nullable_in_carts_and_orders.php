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
        // 1. Modify carts table
        Schema::table('carts', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->change();
            $table->string('session_id', 100)->nullable()->after('id')->index('ix_carts_session_id');
        });

        // 2. Modify orders table
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('carts', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable(false)->change();
            $table->dropIndex('ix_carts_session_id');
            $table->dropColumn('session_id');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable(false)->change();
        });
    }
};
