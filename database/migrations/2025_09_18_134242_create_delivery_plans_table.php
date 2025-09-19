<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('delivery_plans', function (Blueprint $table) {
            $table->id();

            // Multi-tenant + scoping you already use
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('negotiation_id')->constrained()->cascadeOnDelete();

            // Authoring/ownership
            $table->foreignId('created_by');
            $table->foreignId('updated_by');

            // Core plan metadata
            $table->string('title');
            $table->text('summary')->nullable();

            // What kind of delivery is this?
            // Feel free to extend; enum() if you prefer native enums.
            $table->string('category')->nullable(); // e.g., 'subject_supply','hostage_release','evidence_exchange'

            // Status workflow
            $table->string('status')->default('pending');

            // Timing
            $table->timestamp('scheduled_at')->nullable();
            $table->time('window_starts_at')->nullable();
            $table->time('window_ends_at')->nullable();

            // Location / route
            $table->string('location_name')->nullable();
            $table->string('location_address')->nullable();
            $table->json('geo')->nullable(); // {lat,lng,geojson,...}
            $table->json('route')->nullable(); // ordered steps/checkpoints

            // Operational details
            $table->json('instructions')->nullable(); // free-form structured instructions
            $table->json('constraints')->nullable();  // “no radios”, “hands visible”, etc.
            $table->json('contingencies')->nullable(); // fallback plans
            $table->json('risk_assessment')->nullable(); // ratings/notes
            $table->json('signals')->nullable(); // prearranged signals/phrases

            // Auditing
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_plans');
    }
};
