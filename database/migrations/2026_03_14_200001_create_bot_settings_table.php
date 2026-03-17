<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bot_settings', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->text('value');
            $table->timestamps();
        });

        DB::table('bot_settings')->insert([
            ['key' => 'max_tokens', 'value' => '512', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('bot_settings');
    }
};
