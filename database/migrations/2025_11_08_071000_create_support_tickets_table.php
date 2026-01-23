<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('support_tickets')) {
            return;
        }
        Schema::create('support_tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('ticket_number')->unique(); // شماره تیکت یونیک
            $table->string('subject'); // موضوع
            $table->text('message'); // متن تیکت
            $table->enum('status', ['pending', 'answered'])->default('pending'); // وضعیت
            $table->text('admin_reply')->nullable(); // پاسخ ادمین
            $table->timestamp('replied_at')->nullable(); // زمان پاسخ
            $table->timestamps();
            
            $table->index(['user_id', 'created_at']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('support_tickets');
    }
};
