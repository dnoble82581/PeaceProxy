<?php

namespace Database\Factories;

use App\Models\AssessmentTemplate;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class AssessmentTemplateFactory extends Factory
{
    protected $model = AssessmentTemplate::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->word(),
            'description' => $this->faker->text(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

            'tenant_id' => Tenant::factory(),
        ];
    }
}
