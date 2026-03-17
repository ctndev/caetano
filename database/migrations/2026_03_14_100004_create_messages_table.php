<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('allowed_number_id')->nullable()->constrained()->nullOnDelete();
            $table->string('direction'); // in, out
            $table->text('content');
            $table->string('type')->default('text'); // text, audio
            $table->json('ai_response')->nullable();
            $table->timestamps();

            $table->index('direction');
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
