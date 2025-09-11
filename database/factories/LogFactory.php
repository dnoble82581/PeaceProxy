<?php

namespace Database\Factories;

use App\Models\Log;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class LogFactory extends Factory
{
    protected $model = Log::class;

    public function definition(): array
    {
        return [
            'event' => $this->faker->word(),
            'channel' => $this->faker->word(),
            'severity' => $this->faker->word(),
            'headline' => $this->faker->word(),
            'description' => $this->faker->text(),
            'properties' => $this->faker->words(),
            'ipAddress' => $this->faker->ipv4(),
            'user_agent' => $this->faker->word(),
            'occured_at' => Carbon::now(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

            'tenant_id' => Tenant::factory(),
        ];
    }
}
