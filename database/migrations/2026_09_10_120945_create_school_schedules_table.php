<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('school_schedules', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('subject_name');
            $table->string('course_code')->nullable();
            $table->string('class_code')->nullable();

            $table->date('study_date');

            $table->time('start_time');
            $table->time('end_time');

            $table->string('room')->nullable();
            $table->string('instructor')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('school_schedules');
    }
};