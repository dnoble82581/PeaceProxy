<?php

namespace Database\Factories;

use App\Models\Call;
use App\Models\Negotiation;
use App\Models\Subject;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class CallFactory extends Factory
{
    protected $model = Call::class;

    public function definition(): array
    {
        return [
            'duration' => $this->faker->word(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

            'tenant_id' => Tenant::factory(),
            'subject_id' => Subject::factory(),
            'negotiation_id' => Negotiation::factory(),
        ];
    }
}
