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
        if (Schema::hasColumn('questions', 'explanation_image_path')) {
            return;
        }
        Schema::table('questions', function (Blueprint $table) {
            $table->string('explanation_image_path')->nullable()->after('explanation');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropColumn('explanation_image_path');
        });
    }
};
