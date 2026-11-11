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
        Schema::create('exercises', function (Blueprint $table) {
            $table->id();

            // Người tạo bài tập
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            // Bài học liên quan
            $table->foreignId('learning_content_id')
                ->nullable()
                ->constrained('learning_contents')
                ->nullOnDelete();

            // Thông tin bài tập
            $table->string('title');
            $table->text('question');

            // 4 đáp án
            $table->string('option_a');
            $table->string('option_b');
            $table->string('option_c');
            $table->string('option_d');

            // Đáp án đúng: a, b, c hoặc d
            $table->string('correct_option', 1);

            // Giải thích đáp án
            $table->text('explanation')->nullable();

            // beginner / intermediate / advanced
            $table->string('difficulty')->default('beginner');

            // active / inactive
            $table->string('status')->default('active');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exercises');
    }
};