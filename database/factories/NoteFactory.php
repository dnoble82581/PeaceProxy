<?php

namespace Database\Factories;

use App\Models\Negotiation;
use App\Models\Note;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class NoteFactory extends Factory
{
    protected $model = Note::class;

    public function definition(): array
    {
        return [
            'body' => $this->faker->word(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

            'tenant_id' => Tenant::factory(),
            'author_id' => User::factory(),
            'negotiation_id' => Negotiation::factory(),
        ];
    }
}
