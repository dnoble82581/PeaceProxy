<?php

namespace Database\Factories;

use App\Models\moodLog;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class moodLogFactory extends Factory
{
    protected $model = moodLog::class;

    public function definition(): array
    {
        return [
            'mood' => $this->faker->word(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
