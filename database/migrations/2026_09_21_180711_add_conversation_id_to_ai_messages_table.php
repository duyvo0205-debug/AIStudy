<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ai_messages', function (Blueprint $table) {
            $table->foreignId('conversation_id')
                ->nullable()
                ->after('user_id')
                ->constrained('ai_conversations')
                ->cascadeOnDelete();
        });

        // Chuyển các tin nhắn cũ vào một cuộc trò chuyện mặc định
        $userIds = DB::table('ai_messages')
            ->select('user_id')
            ->distinct()
            ->pluck('user_id');

        foreach ($userIds as $userId) {
            $conversationId = DB::table('ai_conversations')->insertGetId([
                'user_id' => $userId,
                'title' => 'Lịch sử trò chuyện',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('ai_messages')
                ->where('user_id', $userId)
                ->whereNull('conversation_id')
                ->update([
                    'conversation_id' => $conversationId,
                ]);
        }
    }

    public function down(): void
    {
        Schema::table('ai_messages', function (Blueprint $table) {
            $table->dropForeign(['conversation_id']);
            $table->dropColumn('conversation_id');
        });
    }
};