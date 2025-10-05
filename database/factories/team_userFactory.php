<?php

namespace Database\Factories;

use App\Models\Team;
use App\Models\team_user;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class team_userFactory extends Factory
{
    protected $model = team_user::class;

    public function definition(): array
    {
        return [
            'is_primary' => $this->faker->boolean(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

            'team_id' => Team::factory(),
            'user_id' => User::factory(),
        ];
    }
}
