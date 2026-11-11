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
        Schema::create('ai_study_recommendations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('school_schedule_id')
                ->nullable()
                ->constrained('school_schedules')
                ->nullOnDelete();

            $table->string('title');

            $table->string('subject')->nullable();

            $table->text('description')->nullable();

            $table->date('recommended_date');

            $table->time('recommended_start_time');

            $table->time('recommended_end_time');

            $table->unsignedInteger('duration_minutes');

            $table->text('learning_goal')->nullable();

            $table->text('reason')->nullable();

            $table->enum('status', [
                'pending',
                'accepted',
                'rejected',
            ])->default('pending');

            $table->timestamps();

            $table->index([
                'user_id',
                'status',
            ]);

            $table->index([
                'recommended_date',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_study_recommendations');
    }
};