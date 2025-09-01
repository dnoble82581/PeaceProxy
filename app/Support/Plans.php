<?php

namespace App\Support;

use App\Models\Tenant;

class Plans
{
    public static function hasFeature(Tenant $tenant, string $feature): bool
    {
        $sub = $tenant->subscription('default');
        if (! $sub) {
            return false;
        }

        if ($tenant->onTrial('default') || $sub->onGracePeriod()) {
            return true;
        } // optional policy

        $priceId = $sub->stripe_price;
        $matrix = config('features.matrix', []);

        return in_array($feature, $matrix[$priceId] ?? [], true);
    }
}
