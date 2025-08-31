<?php

return [
    'stripe' => [
        'model' => App\Models\Tenant::class,   // since you’re Tenant-first
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
    ],
];
