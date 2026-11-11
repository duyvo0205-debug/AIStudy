<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exercise_questions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('exercise_id')
                ->constrained('exercises')
                ->cascadeOnDelete();

            $table->text('question');

            $table->enum('question_type', [
                'multiple_choice',
                'multiple_select',
                'true_false',
                'essay',
            ])->default('multiple_choice');

            $table->json('options')->nullable();

            $table->json('correct_answer')->nullable();

            $table->text('explanation')->nullable();

            $table->decimal('points', 5, 2)->default(1);

            $table->unsignedInteger('sort_order')->default(0);

            $table->timestamps();

            $table->index([
                'exercise_id',
                'sort_order',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exercise_questions');
    }
};