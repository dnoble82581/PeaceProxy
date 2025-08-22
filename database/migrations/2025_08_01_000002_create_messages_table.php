<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained()->cascadeOnDelete();
            $table->index(['conversation_id', 'id']);
            $table->index(['conversation_id', 'created_at']);
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('negotiation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('content');
            $table->foreignId('whisper_to')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('is_whisper')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropIndex(['conversation_id', 'id']);
            $table->dropIndex(['conversation_id', 'created_at']);
        });
    }
};
