<?php

use App\Enums\Hook\HookCategories;
use App\Enums\Hook\HookSensitivityLevels;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('hooks', function (Blueprint $table) {
            $table->id();
            // Foreign keys
            $table->unsignedBigInteger('tenant_id'); // For multi-tenancy
            $table->unsignedBigInteger('subject_id')->index();
            $table->unsignedBigInteger('created_by_id')->nullable();
            $table->unsignedBigInteger('negotiation_id')->nullable();

            // Core fields
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('category', array_map(
                fn ($category) => $category->value,
                HookCategories::cases()
            ))->default(HookCategories::personal); // e.g., Personal, Shared Interest, etc.
            $table->enum('sensitivity_level', array_map(
                fn ($level) => $level->value,
                HookSensitivityLevels::cases()
            ))->default(HookSensitivityLevels::low->value);
            $table->string('source')->nullable(); // e.g. "Phone Call", "Background Check"
            $table->float('confidence_score')->nullable(); // 0.0–1.0

            // Timestamps and softly deletes
            $table->timestamps();
            $table->softDeletes();

            // Indexes and foreign keys (assuming cascading is wanted)
            $table->foreign('subject_id')->references('id')->on('subjects')->cascadeOnDelete();
            $table->foreign('created_by_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('negotiation_id')->references('id')->on('negotiations')->nullOnDelete();
            $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnDelete();
            $table->index('title');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hooks');
    }
};
