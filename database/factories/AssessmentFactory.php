<?php

namespace Database\Factories;

use App\Models\Assessment;
use App\Models\Negotiation;
use App\Models\Subject;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class AssessmentFactory extends Factory{
    protected $model = Assessment::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->word(),//
'created_at' => Carbon::now(),
'updated_at' => Carbon::now(),

'tenant_id' => Tenant::factory(),
'negotiation_id' => Negotiation::factory(),
'subject_id' => Subject::factory(),
        ];
    }
}
