<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->string('status')->default('pending');
            $table->decimal('total_price', 10, 2)->default(0);
            $table->date('delivery_date')->nullable();
            $table->string('payment_status')->default('pending');
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('payment_status');
            $table->index('delivery_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
