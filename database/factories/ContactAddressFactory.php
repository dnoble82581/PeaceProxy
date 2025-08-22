<?php

namespace Database\Factories;

use App\Models\ContactAddress;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class ContactAddressFactory extends Factory
{
    protected $model = ContactAddress::class;

    public function definition(): array
    {
        return [
            'address1' => $this->faker->address(),
            'address2' => $this->faker->address(),
            'city' => $this->faker->city(),
            'region' => $this->faker->word(),
            'postal_code' => $this->faker->postcode(),
            'country_iso' => $this->faker->word(),
            'latitude' => $this->faker->latitude(),
            'longitude' => $this->faker->longitude(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
