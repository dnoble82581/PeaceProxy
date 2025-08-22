<?php

namespace Database\Factories;

use App\Models\ContactEmail;
use App\Models\Negotiation;
use App\Models\Subject;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class ContactEmailFactory extends Factory
{
    protected $model = ContactEmail::class;

    public function definition(): array
    {
        return [
            'email' => $this->faker->unique()->safeEmail(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

            'subject_id' => Subject::factory(),
            'tenant_id' => Tenant::factory(),
            'negotiation_id' => Negotiation::factory(),
        ];
    }
}
