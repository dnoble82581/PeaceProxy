<?php

namespace Database\Factories;

use App\Enums\Negotiation\NegotiationStatuses;
use App\Enums\Negotiation\NegotiationTypes;
use App\Models\Negotiation;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class NegotiationFactory extends Factory
{
    protected $model = Negotiation::class;

    public function definition(): array
    {
        return [
            'tenant_id' => Tenant::factory(),
            'title' => $this->faker->sentence(3),
            'summary' => $this->faker->optional(0.8)->paragraph(),

            // Timing
            'started_at' => $this->faker->optional(0.7)->dateTimeThisMonth(),
            'ended_at' => $this->faker->optional(0.3)->dateTimeThisMonth(),
            'duration_minutes' => $this->faker->optional(0.6)->numberBetween(15, 480),

            // Location
            'location' => $this->faker->optional(0.7)->company(),
            'location_address' => $this->faker->optional(0.7)->streetAddress(),
            'location_city' => $this->faker->optional(0.7)->city(),
            'location_state' => $this->faker->optional(0.7)->stateAbbr(),
            'location_zip' => $this->faker->optional(0.7)->randomNumber(5),

            // Status and Type
            'status' => $this->faker->randomElement(array_map(
                fn ($status) => $status->value,
                NegotiationStatuses::cases()
            )),
            'type' => $this->faker->randomElement(array_map(
                fn ($type) => $type->value,
                NegotiationTypes::cases()
            )),

            // Details
            'initial_complaint' => $this->faker->optional(0.6)->paragraph(),
            'negotiation_strategy' => $this->faker->optional(0.5)->paragraph(),
            'tags' => $this->faker->optional(0.4)->randomElements(['urgent', 'high_risk', 'armed', 'mental_health', 'domestic', 'public_venue'], $this->faker->numberBetween(1, 3)),

            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }

    /**
     * Configure the factory to create an active negotiation.
     */
    public function active(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => NegotiationStatuses::active->value,
                'ended_at' => null,
            ];
        });
    }

    /**
     * Configure the factory to create a resolved negotiation.
     */
    public function resolved(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => NegotiationStatuses::resolved->value,
                'ended_at' => Carbon::now(),
            ];
        });
    }
}
