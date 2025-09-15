<?php

namespace Database\Factories;

use App\Models\Negotiation;
use App\Models\RequestForInformation;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class RequestForInformationFactory extends Factory
{
    protected $model = RequestForInformation::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->word(),
            'body' => $this->faker->word(),
            'status' => $this->faker->word(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

            'tenant_id' => Tenant::factory(),
            'negotiation_id' => Negotiation::factory(),
            'user_id' => User::factory(),
        ];
    }
}
