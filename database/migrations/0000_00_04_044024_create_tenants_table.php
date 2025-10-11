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
            $table->string('agency_email');
            $table->string('agency_phone')->nullable();
            $table->string('agency_website')->nullable();

            // Address
            $table->string('billing_email')->nullable();
            $table->string('billing_phone')->nullable();
            $table->string('tax_id')->nullable();              // VAT/EIN if you enable Stripe Tax
            $table->string('address_line1')->nullable();
            $table->string('address_line2')->nullable();
            $table->string('address_city')->nullable();
            $table->string('address_state')->nullable();
            $table->string('address_postal')->nullable();
            $table->string('address_country', 2)->nullable();

            // Agency Identifiers
            $table->string('agency_identifier')->nullable(); // internal code or registration number
            $table->string('federal_agency_code')->nullable(); // e.g., ORI (FBI Originating Agency Identifier)

            // Timezone and Locale
            $table->string('timezone')->default('America/Chicago');
            $table->string('locale')->default('en');

            // Feature Toggles / Subscription
            $table->boolean('is_active')->default(true);
            $table->boolean('onboarding_complete')->default(false);
            $table->string('stripe_id')->nullable();           // set utf8_bin collation in MySQL
            $table->string('pm_type')->nullable();
            $table->string('pm_last_four', 4)->nullable();
            $table->timestamp('trial_ends_at')->nullable();
            $table->foreignId('billing_owner_id')->nullable()
                ->constrained('users')->nullOnDelete();

            // Branding
            $table->string('logo_path')->nullable();
            $table->string('primary_color')->nullable();
            $table->string('secondary_color')->nullable();

            // Multi-Tenancy Support
            $table->json('settings')->nullable(); // Flexible per-tenant config
            $table->timestamps();
        });

        if (Schema::hasColumn('tenants', 'stripe_id')) {
            DB::statement('ALTER TABLE tenants MODIFY stripe_id VARCHAR(255) COLLATE utf8_bin');
        }
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn([
                'stripe_id', 'pm_type', 'pm_last_four', 'trial_ends_at',
                'billing_email', 'billing_phone', 'tax_id',
                'address_line1', 'address_line2', 'address_city', 'address_state',
                'address_postal', 'address_country',
            ]);
        });
    }
};
