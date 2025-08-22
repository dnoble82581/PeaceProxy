<?php

namespace Database\Factories;

use App\Models\Contact;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class ContactFactory extends Factory
{
    protected $model = Contact::class;

    public function definition(): array
    {
        return [
            'type' => $this->faker->randomElement(['email', 'phone', 'address', 'other']),
            'confidence_score' => $this->faker->optional(0.7)->randomFloat(2, 0, 1),
            'is_primary' => $this->faker->boolean(20), // 20% chance of being primary
            'notes' => $this->faker->optional(0.3)->sentence(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            // contactable_id and contactable_type will be set when the contact is associated with a model
        ];
    }

    /**
     * Configure the factory to associate the contact with a Subject.
     *
     * @param \App\Models\Subject $subject
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function forSubject($subject)
    {
        return $this->state(function (array $attributes) use ($subject) {
            return [
                'contactable_id' => $subject->id,
                'contactable_type' => get_class($subject),
            ];
        });
    }

    /**
     * Configure the factory to create a phone contact.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function phone()
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => 'phone',
            ];
        });
    }

    /**
     * Configure the factory to create an email contact.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function email()
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => 'email',
            ];
        });
    }

    /**
     * Configure the factory to create an address contact.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function address()
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => 'address',
            ];
        });
    }

    /**
     * Configure the factory to create a primary contact.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function primary()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_primary' => true,
            ];
        });
    }
}
