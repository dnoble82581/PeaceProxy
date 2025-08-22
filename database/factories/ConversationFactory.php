<?php

namespace Database\Factories;

use App\Models\Conversation;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class ConversationFactory extends Factory
{
    protected $model = Conversation::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'type' => $this->faker->word(),
            'is_active' => $this->faker->boolean(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

            'tenant_id' => Tenant::factory(),
            'created_by' => User::factory(),
        ];
    }
}
