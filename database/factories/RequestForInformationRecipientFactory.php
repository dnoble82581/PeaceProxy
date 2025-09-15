<?php

namespace Database\Factories;

use App\Models\RequestForInformation;
use App\Models\RequestForInformationRecipient;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class RequestForInformationRecipientFactory extends Factory
{
    protected $model = RequestForInformationRecipient::class;

    public function definition(): array
    {
        return [
            'is_read' => $this->faker->boolean(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

            'request_for_information_id' => RequestForInformation::factory(),
            'tenant_id' => Tenant::factory(),
            'user_id' => User::factory(),
        ];
    }
}
