<?php

namespace Database\Factories;

use App\Enums\User\UserNegotiationRole;
use App\Enums\User\UserNegotiationStatuses;
use App\Models\Negotiation;
use App\Models\NegotiationUser;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class NegotiationUserFactory extends Factory
{
    protected $model = NegotiationUser::class;

    public function definition(): array
    {

        return [
            'negotiation_id' => Negotiation::factory(),
            'user_id' => User::factory(),
            'role' => $this->faker->randomElement(array_map(
                fn ($role) => $role->value,
                UserNegotiationRole::cases()
            )),
            'status' => $this->faker->randomElement(array_map(
                fn ($status) => $status->value,
                UserNegotiationStatuses::cases()
            )),
            'joined_at' => Carbon::now()->subMinutes($this->faker->numberBetween(5, 120)),
            'left_at' => $this->faker->optional(0.3)->dateTimeThisMonth(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }

    /**
     * Configure the factory to create an active user in a negotiation.
     */
    public function active(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => UserNegotiationStatuses::active->value,
                'left_at' => null,
            ];
        });
    }

    /**
     * Configure the factory to create an inactive user in a negotiation.
     */
    public function inactive(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => UserNegotiationStatuses::inactive->value,
                'left_at' => Carbon::now(),
            ];
        });
    }
}
