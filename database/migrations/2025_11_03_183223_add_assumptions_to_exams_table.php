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
        if (Schema::hasColumn('exams', 'assumptions_text')) {
            return;
        }
        Schema::table('exams', function (Blueprint $table) {
            $table->text('assumptions_text')->nullable()->after('negative_score_ratio');
            $table->string('assumptions_image')->nullable()->after('assumptions_text');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            $table->dropColumn(['assumptions_text', 'assumptions_image']);
        });
    }
};
