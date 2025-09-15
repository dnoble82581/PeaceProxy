<?php

namespace Database\Factories;

use App\Models\RequestForInformation;
use App\Models\RequestForInformationReply;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class RequestForInformationReplyFactory extends Factory
{
    protected $model = RequestForInformationReply::class;

    public function definition(): array
    {
        return [
            'body' => $this->faker->word(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

            'request_for_information_id' => RequestForInformation::factory(),
            'tenant_id' => Tenant::factory(),
            'user_id' => User::factory(),
        ];
    }
}
