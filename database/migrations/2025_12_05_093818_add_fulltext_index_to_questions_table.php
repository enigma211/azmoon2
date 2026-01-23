<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (config('database.default') !== 'mysql') {
            return;
        }

        $indexes = DB::select("SHOW INDEX FROM questions WHERE Key_name = 'questions_text_fulltext'");
        if (count($indexes) > 0) {
            return;
        }

        Schema::table('questions', function (Blueprint $table) {
            // Add Fulltext index for better search performance
            $table->fullText('text');
            // Also indexing explanation if we want to search there too later
            $table->fullText('explanation');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropFullText(['text']);
            $table->dropFullText(['explanation']);
        });
    }
};
