<?php

namespace Database\Factories;

use App\Enums\Subject\MoodLevels;
use App\Enums\Subject\SubjectNegotiationStatuses;
use App\Models\ContactAddress;
use App\Models\ContactEmail;
use App\Models\ContactPhone;
use App\Models\ContactPoint;
use App\Models\Subject;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class SubjectFactory extends Factory
{
    protected $model = Subject::class;

    /**
     * Configure the factory to create a subject with a phone contact.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function withPhoneContact()
    {
        return $this->afterCreating(function (Subject $subject) {
            // Create a contact point for phone
            $contactPoint = ContactPoint::create([
                'contactable_id' => $subject->id,
                'contactable_type' => get_class($subject),
                'tenant_id' => $subject->tenant_id,
                'kind' => 'phone',
                'label' => 'Primary Phone',
                'is_primary' => true,
                'is_verified' => false,
                'priority' => 1,
            ]);

            // Create a phone for the contact point
            ContactPhone::create([
                'contact_point_id' => $contactPoint->id,
                'e164' => '+1' . $this->faker->numerify('##########'),
                'ext' => null,
                'country_iso' => 'US',
            ]);
        });
    }

    /**
     * Configure the factory to create a subject with an email contact.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function withEmailContact()
    {
        return $this->afterCreating(function (Subject $subject) {
            // Create a contact point for email
            $contactPoint = ContactPoint::create([
                'contactable_id' => $subject->id,
                'contactable_type' => get_class($subject),
                'tenant_id' => $subject->tenant_id,
                'kind' => 'email',
                'label' => 'Primary Email',
                'is_primary' => true,
                'is_verified' => false,
                'priority' => 1,
            ]);

            // Create an email for the contact point
            ContactEmail::create([
                'contact_point_id' => $contactPoint->id,
                'email' => $this->faker->safeEmail(),
            ]);
        });
    }

    /**
     * Configure the factory to create a subject with an address contact.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function withAddressContact()
    {
        return $this->afterCreating(function (Subject $subject) {
            // Create a contact point for address
            $contactPoint = ContactPoint::create([
                'contactable_id' => $subject->id,
                'contactable_type' => get_class($subject),
                'tenant_id' => $subject->tenant_id,
                'kind' => 'address',
                'label' => 'Primary Address',
                'is_primary' => true,
                'is_verified' => false,
                'priority' => 1,
            ]);

            // Create an address for the contact point
            ContactAddress::create([
                'contact_point_id' => $contactPoint->id,
                'address1' => $this->faker->streetAddress(),
                'address2' => $this->faker->optional(0.3)->secondaryAddress(),
                'city' => $this->faker->city(),
                'region' => $this->faker->state(),
                'postal_code' => $this->faker->postcode(),
                'country_iso' => 'US',
                'latitude' => $this->faker->optional(0.7)->latitude(),
                'longitude' => $this->faker->optional(0.7)->longitude(),
            ]);
        });
    }

    /**
     * Configure the factory to create a subject with all types of contacts.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function withAllContacts()
    {
        return $this->afterCreating(function (Subject $subject) {
            // Create a contact point for phone
            $phoneContactPoint = ContactPoint::create([
                'contactable_id' => $subject->id,
                'contactable_type' => get_class($subject),
                'tenant_id' => $subject->tenant_id,
                'kind' => 'phone',
                'label' => 'Primary Phone',
                'is_primary' => true,
                'is_verified' => false,
                'priority' => 1,
            ]);

            // Create a phone for the contact point
            ContactPhone::create([
                'contact_point_id' => $phoneContactPoint->id,
                'e164' => '+1' . $this->faker->numerify('##########'),
                'ext' => null,
                'country_iso' => 'US',
            ]);

            // Create a contact point for email
            $emailContactPoint = ContactPoint::create([
                'contactable_id' => $subject->id,
                'contactable_type' => get_class($subject),
                'tenant_id' => $subject->tenant_id,
                'kind' => 'email',
                'label' => 'Primary Email',
                'is_primary' => false,
                'is_verified' => false,
                'priority' => 2,
            ]);

            // Create an email for the contact point
            ContactEmail::create([
                'contact_point_id' => $emailContactPoint->id,
                'email' => $this->faker->safeEmail(),
            ]);

            // Create a contact point for address
            $addressContactPoint = ContactPoint::create([
                'contactable_id' => $subject->id,
                'contactable_type' => get_class($subject),
                'tenant_id' => $subject->tenant_id,
                'kind' => 'address',
                'label' => 'Primary Address',
                'is_primary' => false,
                'is_verified' => false,
                'priority' => 3,
            ]);

            // Create an address for the contact point
            ContactAddress::create([
                'contact_point_id' => $addressContactPoint->id,
                'address1' => $this->faker->streetAddress(),
                'address2' => $this->faker->optional(0.3)->secondaryAddress(),
                'city' => $this->faker->city(),
                'region' => $this->faker->state(),
                'postal_code' => $this->faker->postcode(),
                'country_iso' => 'US',
                'latitude' => $this->faker->optional(0.7)->latitude(),
                'longitude' => $this->faker->optional(0.7)->longitude(),
            ]);
        });
    }

    public function definition(): array
    {
        $genders = ['Male', 'Female', 'Non-binary', 'Other', 'Prefer not to say'];
        $hairColors = ['Black', 'Brown', 'Blonde', 'Red', 'Gray', 'White', 'Other'];
        $eyeColors = ['Brown', 'Blue', 'Green', 'Hazel', 'Gray', 'Other'];

        return [
            'tenant_id' => Tenant::factory(),
            'name' => $this->faker->name(),
            'alias' => $this->faker->randomElements(['Johnny', 'Jane', 'Bob', 'Sally', 'Mary'], 2),
            'date_of_birth' => $this->faker->optional(0.7)->dateTimeBetween('-70 years', '-18 years'),
            'gender' => $this->faker->optional(0.7)->randomElement($genders),

            // Physical characteristics
            'height' => $this->faker->optional(0.6)->regexify('[5-6]\'[0-9]"'),
            'weight' => $this->faker->optional(0.6)->numberBetween(100, 300),
            'hair_color' => $this->faker->optional(0.6)->randomElement($hairColors),
            'eye_color' => $this->faker->optional(0.6)->randomElement($eyeColors),
            'identifying_features' => $this->faker->optional(0.4)->sentence(),

            // Contact information is now handled through Contact, PhoneNumber, Email, and Address models

            // Background information
            'occupation' => $this->faker->optional(0.6)->jobTitle(),
            'employer' => $this->faker->optional(0.5)->company(),
            'mental_health_history' => $this->faker->optional(0.4)->paragraph(),
            'criminal_history' => $this->faker->optional(0.4)->paragraph(),
            'substance_abuse_history' => $this->faker->optional(0.4)->paragraph(),
            'known_weapons' => $this->faker->optional(0.3)->sentence(),

            // Risk assessment
            'risk_factors' => $this->faker->randomElements([
                'violent_history', 'suicidal', 'substance_abuse', 'mental_illness',
                'access_to_weapons', 'hostility_to_law_enforcement', 'prior_arrests',
            ], $this->faker->numberBetween(1, 4)),

            // Notes and status
            'notes' => $this->faker->optional(0.6)->paragraph(),
            'current_mood' => $this->faker->randomElement(array_map(
                fn ($mood) => $mood->value,
                MoodLevels::cases()
            )),
            'status' => $this->faker->randomElement(array_map(
                fn ($status) => $status->value,
                SubjectNegotiationStatuses::cases()
            )),

            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
