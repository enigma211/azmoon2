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
        Schema::table('exam_attempts', function (Blueprint $table) {
            // Check if the index exists before dropping to avoid errors if it was named differently or doesn't exist
            // But the error log explicitly named 'exam_attempts_exam_id_user_id_unique'
            $table->dropUnique('exam_attempts_exam_id_user_id_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exam_attempts', function (Blueprint $table) {
            $table->unique(['exam_id', 'user_id']);
        });
    }
};
