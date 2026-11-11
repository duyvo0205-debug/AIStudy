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
        Schema::table('study_schedules', function (Blueprint $table) {
            $table->string('subject')->nullable()->after('title');

            $table->text('learning_goal')
                ->nullable()
                ->after('subject');

            $table->text('study_content')
                ->nullable()
                ->after('learning_goal');

            $table->text('exercises')
                ->nullable()
                ->after('study_content');

            $table->text('materials')
                ->nullable()
                ->after('exercises');

            $table->enum('priority', [
                'low',
                'medium',
                'high'
            ])
                ->default('medium')
                ->after('materials');

            $table->text('result_note')
                ->nullable()
                ->after('description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('study_schedules', function (Blueprint $table) {
            $table->dropColumn([
                'subject',
                'learning_goal',
                'study_content',
                'exercises',
                'materials',
                'result_note',
                'priority',
            ]);
        });
    }
};