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
        if (Schema::hasColumn('system_settings', 'terms_content')) {
            return;
        }
        Schema::table('system_settings', function (Blueprint $table) {
            $table->longText('terms_content')->nullable()->after('free_trial_hours');
            $table->longText('about_content')->nullable()->after('terms_content');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('system_settings', function (Blueprint $table) {
            $table->dropColumn(['terms_content', 'about_content']);
        });
    }
};
