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
        Schema::create('user_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('receiver_name', 100);
            $table->string('receiver_phone', 20);
            $table->string('address', 255);
            $table->string('commune', 100);
            $table->string('city', 100);
            $table->boolean('is_default')->default(false);
            $table->timestamps();

            $table->index('user_id', 'ix_user_addresses_user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_addresses');
    }
};
