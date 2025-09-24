<?php

namespace App\Providers;

use App\Models\Tenant;
use Illuminate\Support\ServiceProvider;
use Laravel\Cashier\Cashier;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if (app()->environment('production')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        Cashier::useCustomerModel(Tenant::class);   // <-- critical

        //        TallStackUi::personalize()
        //            ->form('carousel')
        //            ->block('wrapper')
        //            ->replace('rounded-md', 'rounded-full');
    }
}
