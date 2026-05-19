<?php

namespace App\Services\Billing;

use Exception;
use Laravel\Cashier\Checkout;

class StripeService
{
    public function __construct()
    {
    }

    /**
     * @throws Exception
     */
    public function startSubscription(string $priceId): Checkout
    {
        return tenant()->newSubscription('default', $priceId)
            ->allowPromotionCodes()
            ->checkout([
                'success_url' => route('billing.index', ['tenantSubdomain' => tenant()->subdomain]),
                'cancel_url' => route('dashboard', ['tenantSubdomain' => tenant()->subdomain]),
            ]);
    }
}
