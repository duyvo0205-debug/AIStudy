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
            $table->enum('source', [
                'manual',
                'ai',
            ])
                ->default('manual')
                ->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('study_schedules', function (Blueprint $table) {
            $table->dropColumn('source');
        });
    }
};