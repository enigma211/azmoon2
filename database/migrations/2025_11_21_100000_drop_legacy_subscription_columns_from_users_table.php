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
        if (!Schema::hasColumn('users', 'subscription_plan_id')) {
            return;
        }
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['subscription_plan_id']);
            $table->dropColumn(['subscription_plan_id', 'subscription_start', 'subscription_end']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('subscription_plan_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamp('subscription_start')->nullable();
            $table->timestamp('subscription_end')->nullable();
        });
    }
};
