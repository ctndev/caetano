<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('amount_paid', 10, 2)->default(0)->after('total_price');
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 10, 2);
            $table->string('method')->default('other');
            $table->text('notes')->nullable();
            $table->timestamp('paid_at')->useCurrent();
            $table->timestamps();

            $table->index('paid_at');
        });

        Schema::table('messages', function (Blueprint $table) {
            $table->unsignedInteger('prompt_tokens')->default(0)->after('ai_response');
            $table->unsignedInteger('completion_tokens')->default(0)->after('prompt_tokens');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('amount_paid');
        });

        Schema::dropIfExists('payments');

        Schema::table('messages', function (Blueprint $table) {
            $table->dropColumn(['prompt_tokens', 'completion_tokens']);
        });
    }
};
