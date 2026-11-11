<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exercise_attempts', function (Blueprint $table) {
            $table->foreignId('exercise_question_id')
                ->nullable()
                ->after('exercise_id')
                ->constrained('exercise_questions')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('exercise_attempts', function (Blueprint $table) {
            $table->dropForeign(['exercise_question_id']);
            $table->dropColumn('exercise_question_id');
        });
    }
};