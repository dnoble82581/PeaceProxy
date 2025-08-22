<?php

namespace Database\Factories;

use App\Models\Address;
use App\Models\Contact;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class AddressFactory extends Factory
{
    protected $model = Address::class;

    public function definition(): array
    {
        return [
            'tenant_id' => Tenant::factory(),
            'address_1' => $this->faker->streetAddress(),
            'address_2' => $this->faker->optional(0.3)->secondaryAddress(),
            'city' => $this->faker->city(),
            'state' => $this->faker->stateAbbr(),
            'postal_code' => $this->faker->postcode(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'contact_id' => Contact::factory(),
        ];
    }

    /**
     * Configure the factory to associate the address with an existing contact.
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
