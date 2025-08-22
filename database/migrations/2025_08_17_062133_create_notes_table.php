<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('negotiation_id')->nullable()->constrained('negotiations');
            $table->index('negotiation_id');
            $table->foreignId('tenant_id')->constrained('tenants');
            $table->index('tenant_id');
            $table->foreignId('author_id')->constrained('users');
            $table->index('author_id');

            // Note content
            $table->string('title')->nullable();
            $table->text('body');

            // Authorship


            // Optional flags
            $table->boolean('is_private')->default(false);
            $table->boolean('pinned')->default(false);

            // Tags (JSON, no index unless you're querying by JSON fields)
            $table->json('tags')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notes');
    }
};
