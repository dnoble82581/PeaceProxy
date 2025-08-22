<?php

namespace Database\Factories;

use App\Enums\Subject\SubjectNegotiationRoles;
use App\Models\Negotiation;
use App\Models\NegotiationSubject;
use App\Models\Subject;
use Illuminate\Database\Eloquent\Factories\Factory;

class NegotiationSubjectFactory extends Factory
{
    protected $model = NegotiationSubject::class;

    public function definition(): array
    {
        return [
            'negotiation_id' => Negotiation::factory(),
            'subject_id' => Subject::factory(),
            'role' => $this->faker->randomElement(array_map(
                fn ($role) => $role->value,
                SubjectNegotiationRoles::cases()
            )),
        ];
    }

    /**
     * Configure the factory to create a primary subject in a negotiation.
     */
    public function primary(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'role' => SubjectNegotiationRoles::primary->value,
            ];
        });
    }

    /**
     * Configure the factory to create a secondary subject in a negotiation.
     */
    public function secondary(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'role' => SubjectNegotiationRoles::secondary->value,
            ];
        });
    }

    /**
     * Configure the factory to create a tertiary subject in a negotiation.
     */
    public function tertiary(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'role' => SubjectNegotiationRoles::tertiary->value,
            ];
        });
    }
}
