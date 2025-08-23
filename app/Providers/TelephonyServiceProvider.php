<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class TelephonyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(\App\Contracts\CallRepositoryInterface::class, \App\Repositories\Call\CallRepository::class);
        $this->app->bind(\App\Contracts\CallRecordingRepositoryInterface::class, \App\Repositories\Call\CallRecordingRepository::class);
        $this->app->bind(\App\Contracts\CallEventRepositoryInterface::class, \App\Repositories\Call\CallEventRepository::class);

        $this->app->singleton(
            \Twilio\Rest\Client::class,
            fn () =>
        new \Twilio\Rest\Client(config('twilio.account_sid'), config('twilio.auth_token'))
        );
    }

    public function boot(): void
    {
    }
}
