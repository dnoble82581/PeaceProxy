<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            // Core Identification
            $table->string('agency_name');
            $table->string('subdomain')->unique();
            $table->string('agency_type'); // e.g., 'law_enforcement', 'mental_health'

            // Contact Info
            $table->string('agency_email')->nullable();
            $table->string('agency_phone')->nullable();
            $table->string('agency_website')->nullable();

            // Address
            $table->string('address_line_1')->nullable();
            $table->string('address_line_2')->nullable();
            $table->string('city')->nullable();
            $table->string('state', 2)->nullable(); // US State Code
            $table->string('postal_code')->nullable();
            $table->string('country')->default('US');

            // Agency Identifiers
            $table->string('agency_identifier')->nullable(); // internal code or registration number
            $table->string('federal_agency_code')->nullable(); // e.g., ORI (FBI Originating Agency Identifier)

            // Timezone and Locale
            $table->string('timezone')->default('America/Chicago');
            $table->string('locale')->default('en');

            // Feature Toggles / Subscription
            $table->boolean('is_active')->default(true);
            $table->boolean('onboarding_complete')->default(false);
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('subscription_ends_at')->nullable();

            // Branding
            $table->string('logo_path')->nullable();
            $table->string('primary_color')->nullable();
            $table->string('secondary_color')->nullable();

            // Multi-Tenancy Support
            $table->json('settings')->nullable(); // Flexible per-tenant config
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
};
