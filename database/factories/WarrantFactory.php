<?php

namespace Database\Factories;

use App\Models\Subject;
use App\Models\Tenant;
use App\Models\Warrant;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class WarrantFactory extends Factory
{
    protected $model = Warrant::class;

    public function definition(): array
    {
        return [
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

            'tenant_id' => Tenant::factory(),
            'subject_id' => Subject::factory(),
            'created_by' => \App\Models\User::factory(),
        ];
    }
}
