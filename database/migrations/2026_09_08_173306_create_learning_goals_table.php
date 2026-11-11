<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('learning_goals', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('title');

            $table->text('description')->nullable();

            $table->string('subject')->nullable();

            $table->enum('current_level', [
                'beginner',
                'intermediate',
                'advanced'
            ])->default('beginner');

            $table->enum('target_level', [
                'beginner',
                'intermediate',
                'advanced'
            ])->default('intermediate');

            $table->date('target_date')->nullable();

            $table->unsignedInteger('daily_minutes')->default(60);

            $table->enum('status', [
                'active',
                'completed',
                'paused'
            ])->default('active');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('learning_goals');
    }
};