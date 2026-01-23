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
        try {
            Schema::table('exam_attempts', function (Blueprint $table) {
                $table->dropUnique('exam_attempts_exam_id_user_id_unique');
            });
        } catch (\Exception $e) {
            // Index probably doesn't exist
        }
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
