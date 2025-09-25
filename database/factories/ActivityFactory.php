<?php

namespace Database\Factories;

use App\Models\Activity;
use App\Models\Negotiation;
use App\Models\Subject;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class ActivityFactory extends Factory
{
    protected $model = Activity::class;

    public function definition(): array
    {
        return [
            'type' => $this->faker->word(),
            'activity' => $this->faker->word(),
            'is_flagged' => $this->faker->boolean(),
            'entered_at' => Carbon::now(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

            'tenant_id' => Tenant::factory(),
            'negotiation_id' => Negotiation::factory(),
            'user_id' => User::factory(),
            'subject_id' => Subject::factory(),
        ];
    }
}
