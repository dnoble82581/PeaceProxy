<?php

namespace Database\Factories;

use App\Models\Contact;
use App\Models\PhoneNumber;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class PhoneNumberFactory extends Factory
{
    protected $model = PhoneNumber::class;

    public function definition(): array
    {
        return [
            'tenant_id' => Tenant::factory(),
            'phone_number' => $this->faker->phoneNumber(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'contact_id' => Contact::factory(),
        ];
    }

    /**
     * Configure the factory to associate the phone number with an existing contact.
     *
     * @param \App\Models\Contact $contact
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function forContact($contact)
    {
        return $this->state(function (array $attributes) use ($contact) {
            return [
                'contact_id' => $contact->id,
                'tenant_id' => Tenant::factory(),
            ];
        });
    }
}
