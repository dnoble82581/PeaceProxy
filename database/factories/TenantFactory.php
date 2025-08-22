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

            // Address
            'address_line_1' => $this->faker->streetAddress(),
            'address_line_2' => $this->faker->streetAddress(),
            'city' => $this->faker->city(),
            'state' => $this->faker->stateAbbr(),
            'postal_code' => $this->faker->postcode(),
            'country' => 'US',

            // Agency Identifiers
            'agency_identifier' => $this->faker->regexify('[A-Z]{3}[0-9]{4}'),
            'federal_agency_code' => $this->faker->regexify('[A-Z]{2}[0-9]{7}'),

            // Timezone and Locale
            'timezone' => 'America/Chicago',
            'locale' => 'en',

            // Feature Toggles / Subscription
            'is_active' => true,
            'onboarding_complete' => $this->faker->boolean(30),
            'trial_ends_at' => $this->faker->optional(0.7)->dateTimeBetween('+1 week', '+1 month'),
            'subscription_ends_at' => $this->faker->dateTimeBetween('+1 month', '+1 year'),

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
