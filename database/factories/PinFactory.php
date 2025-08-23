<?php

namespace Database\Factories;

use App\Models\Pin;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class PinFactory extends Factory
{
    protected $model = Pin::class;

    public function definition(): array
    {
        return [
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

            'tenant_id' => Tenant::factory(),
            'user_id' => User::factory(),
        ];
    }
}
