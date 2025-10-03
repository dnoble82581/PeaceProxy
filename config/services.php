<?php

return [
    'stripe' => [
        'model' => App\Models\Tenant::class,   // since you’re Tenant-first
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
    ],

    'maps' => [
        'js_key' => env('GOOGLE_MAPS_API_KEY'),
    ],
];
