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
        if (Schema::hasColumn('system_settings', 'free_trial_hours')) {
            return;
        }
        Schema::table('system_settings', function (Blueprint $table) {
            $table->integer('free_trial_hours')->default(48)->after('value');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('system_settings', function (Blueprint $table) {
            $table->dropColumn('free_trial_hours');
        });
    }
};
