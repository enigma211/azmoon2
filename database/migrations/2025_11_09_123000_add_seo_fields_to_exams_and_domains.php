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
        // Check if columns don't exist before adding them
        if (!Schema::hasColumn('exams', 'seo_title')) {
            Schema::table('exams', function (Blueprint $table) {
                $table->string('seo_title')->nullable();
            });
        }
        
        if (!Schema::hasColumn('exams', 'seo_description')) {
            Schema::table('exams', function (Blueprint $table) {
                $table->text('seo_description')->nullable();
            });
        }

        if (!Schema::hasColumn('exam_domains', 'seo_title')) {
            Schema::table('exam_domains', function (Blueprint $table) {
                $table->string('seo_title')->nullable();
            });
        }
        
        if (!Schema::hasColumn('exam_domains', 'seo_description')) {
            Schema::table('exam_domains', function (Blueprint $table) {
                $table->text('seo_description')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            $table->dropColumn(['seo_title', 'seo_description']);
        });

        Schema::table('exam_domains', function (Blueprint $table) {
            $table->dropColumn(['seo_title', 'seo_description']);
        });
    }
};
