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
        Schema::create('exercise_attempts', function (Blueprint $table) {
            $table->id();

            // Người làm bài
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            // Bài tập
            $table->foreignId('exercise_id')
                ->constrained('exercises')
                ->cascadeOnDelete();

            // Đáp án người dùng chọn
            $table->string('selected_option', 1);

            // Đúng / sai
            $table->boolean('is_correct')->default(false);

            // Điểm
            $table->decimal('score', 5, 2)->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exercise_attempts');
    }
};