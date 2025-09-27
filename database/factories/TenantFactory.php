<?php

namespace Database\Factories;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class TenantFactory extends Factory
{
    protected $model = Tenant::class;

    public function definition(): array
    {
        return [
            'agency_name' => $this->faker->company(),
            'subdomain' => $this->faker->unique()->word(),
            'agency_type' => $this->faker->randomElement([
                'law_enforcement', 'mental_health', 'social_services', 'emergency_services',
            ]),

            // Contact Info
            'agency_email' => $this->faker->companyEmail(),
            'agency_phone' => $this->faker->phoneNumber(),
            'agency_website' => $this->faker->url(),

            // Address and Billing
            'billing_email' => $this->faker->safeEmail(),
            'billing_phone' => $this->faker->phoneNumber(),
            'tax_id' => $this->faker->numerify('##-#######'),
            'address_line1' => $this->faker->streetAddress(),
            'address_line2' => $this->faker->streetAddress(),
            'address_city' => $this->faker->city(),
            'address_state' => $this->faker->stateAbbr(),
            'address_postal' => $this->faker->postcode(),
            'address_country' => 'US',

            // Agency Identifiers
            'agency_identifier' => $this->faker->regexify('[A-Z]{3}[0-9]{4}'),
            'federal_agency_code' => $this->faker->regexify('[A-Z]{2}[0-9]{7}'),

            // Timezone and Locale
            'timezone' => 'America/Chicago',
            'locale' => 'en',

            // Feature Toggles / Subscription
            'is_active' => true,
            'onboarding_complete' => $this->faker->boolean(30),
            'stripe_id' => $this->faker->optional(0.7)->regexify('cus_[A-Za-z0-9]{14}'),
            'pm_type' => $this->faker->optional(0.7)->randomElement(['card', 'bank']),
            'pm_last_four' => $this->faker->optional(0.7)->numerify('####'),
            'trial_ends_at' => $this->faker->optional(0.7)->dateTimeBetween('+1 week', '+1 month'),
            'billing_owner_id' => null,

            // Branding
            'logo_path' => $this->faker->imageUrl(200, 50, 'business'),
            'primary_color' => $this->faker->hexColor(),
            'secondary_color' => $this->faker->hexColor(),

            // Multi-Tenancy Support
            'settings' => null,

            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
