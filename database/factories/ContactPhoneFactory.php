<?php

namespace Database\Factories;

use App\Models\ContactPhone;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class ContactPhoneFactory extends Factory
{
    protected $model = ContactPhone::class;

    public function definition(): array
    {
        return [
            'e164' => $this->faker->word(),
            'ext' => $this->faker->word(),
            'country_iso' => $this->faker->word(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
