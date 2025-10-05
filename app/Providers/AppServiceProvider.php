<?php

namespace App\Providers;

use App\Models\Tenant;
use App\Models\Weapon;
use App\Observers\Weapon\WeaponObserver;
use Illuminate\Support\Facades\URL;
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
            URL::forceScheme('https');
        }

        Cashier::useCustomerModel(Tenant::class);   // <-- critical

        Weapon::observe(WeaponObserver::class);
    }
}
