<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// $table->id();
// $table->foreignId('game_id')->constrained()->restrictOnDelete();
// $table->string('name', 100);                    // "86 Diamonds"
// $table->string('image_path')->nullable();
// $table->unsignedInteger('item_amount');         // 86
// $table->decimal('price', 15, 2);                // harga jual
// $table->decimal('original_price', 15, 2)->nullable(); // terisi = promo (harga coret)
// $table->unsignedInteger('stock')->default(0);
// $table->boolean('is_active')->default(true);
// $table->timestamps();
// $table->index(['game_id', 'is_active']);

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('game_id')->constrained()->restrictOnDelete();
            $table->string('name', 100);
            $table->string('image_path')->nullable();
            $table->unsignedInteger('item_amount');
            $table->decimal('price', 15, 2);
            $table->decimal('original_price', 15, 2)->nullable();
            $table->unsignedInteger('stock')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index(['game_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
