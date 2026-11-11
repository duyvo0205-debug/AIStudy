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
    Schema::create('user_profiles', function (Blueprint $table) {
        $table->id();

        $table->foreignId('user_id')
            ->unique()
            ->constrained('users')
            ->cascadeOnDelete();

        $table->string('avatar')->nullable();

        $table->string('phone')->nullable();

        $table->date('date_of_birth')->nullable();

        $table->string('education_level')->nullable();

        $table->string('major')->nullable();

        $table->string('learning_level')->default('beginner');

        $table->unsignedInteger('daily_study_target')->default(60);

        $table->time('preferred_study_start')->nullable();

        $table->time('preferred_study_end')->nullable();

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_profiles');
    }
};
