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
        Schema::create('conversation_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamp('joined_at')->useCurrent();
            $table->timestamp('last_read_at')->nullable()->index();
            $table->foreignId('last_read_message_id')
                ->nullable()
                ->constrained('messages')
                ->nullOnDelete()
                ->index();
            $table->timestamp('left_at')->nullable();
            $table->timestamps();

            $table->unique(['conversation_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('conversation_user', function (Blueprint $table) {
            // Drop FK before column
            $table->dropForeign(['last_read_message_id']);
            $table->dropColumn(['last_read_at', 'last_read_message_id']);
        });
    }
};
