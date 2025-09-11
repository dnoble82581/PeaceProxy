<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('logs', function (Blueprint $table) {
            $table->id();

            // Multi-tenant scope
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();

            // Who/what the log is about (polymorphic target)
            $table->nullableMorphs('loggable'); // loggable_type, loggable_id

            // Who did it (user, system, webhook, bot) — also polymorphic
            $table->nullableMorphs('actor');    // actor_type, actor_id

            // What happened
            $table->string('event', 100);       // e.g. 'message.sent', 'subject.updated'
            $table->string('channel', 50)->default('app'); // app|api|system|webhook
            $table->string('severity', 20)->default('info'); // debug|info|notice|warning|error|critical

            // Human-readable text (for printouts/timelines)
            $table->string('headline');         // short summary for UI cards
            $table->text('description')->nullable();

            // Structured context for reports
            $table->json('properties')->nullable(); // arbitrary key/values (diffs, ids, tags, etc.)

            // Environment
            $table->ipAddress()->nullable();
            $table->string('user_agent')->nullable();

            // Timestamps
            $table->timestamp('occurred_at');   // when it actually happened (not just created_at)
            $table->timestamps();

            // Helpful indexes
            $table->index(['tenant_id', 'occurred_at']);
            $table->index(['tenant_id', 'event']);
            $table->index(['tenant_id', 'loggable_type', 'loggable_id']);
            $table->index(['tenant_id', 'actor_type', 'actor_id']);
            $table->index(['severity']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('logs');
    }
};
