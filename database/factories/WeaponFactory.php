<?php

namespace Database\Factories;

use App\Models\Subject;
use App\Models\Tenant;
use App\Models\Weapon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class WeaponFactory extends Factory
{
    protected $model = Weapon::class;

    public function definition(): array
    {
        return [
            'category' => $this->faker->word(),
            'make' => $this->faker->word(),
            'model' => $this->faker->word(),
            'caliber' => $this->faker->word(),
            'status' => $this->faker->word(),
            'source' => $this->faker->word(),
            'threat_level' => $this->faker->word(),
            'last_seen_at' => Carbon::now(),
            'reported_by_user_id' => $this->faker->word(),
            'description' => $this->faker->text(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

            'subject_id' => Subject::factory(),
            'tenant_id' => Tenant::factory(),
        ];
    }
}
