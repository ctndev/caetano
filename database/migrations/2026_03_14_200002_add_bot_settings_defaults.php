<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $defaults = [
            ['key' => 'ai_model', 'value' => 'gpt-4.1-mini', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'context_timeout_minutes', 'value' => '30', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'max_context_messages', 'value' => '20', 'created_at' => now(), 'updated_at' => now()],
        ];

        foreach ($defaults as $setting) {
            DB::table('bot_settings')->updateOrInsert(
                ['key' => $setting['key']],
                $setting
            );
        }
    }

    public function down(): void
    {
        DB::table('bot_settings')->whereIn('key', [
            'ai_model',
            'context_timeout_minutes',
            'max_context_messages',
        ])->delete();
    }
};
