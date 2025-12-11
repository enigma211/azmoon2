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
        Schema::table('system_settings', function (Blueprint $table) {
            $table->string('site_name')->nullable()->after('value');
            $table->string('site_identity')->nullable()->after('site_name'); // e.g. slogan
            $table->string('seo_title')->nullable()->after('site_identity');
            $table->text('seo_description')->nullable()->after('seo_title');
            $table->text('seo_keywords')->nullable()->after('seo_description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('system_settings', function (Blueprint $table) {
            $table->dropColumn([
                'site_name',
                'site_identity',
                'seo_title',
                'seo_description',
                'seo_keywords',
            ]);
        });
    }
};
