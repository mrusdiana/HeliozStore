<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->string('provider', 30)->default('midtrans');
            $table->string('snap_token')->nullable();
            $table->string('provider_reference', 100)->nullable()->unique();
            $table->string('status', 30); 
            $table->decimal('amount', 15, 2);
            $table->timestamp('paid_at')->nullable();
            $table->json('raw_response')->nullable();  
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
