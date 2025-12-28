<?php

return [
    'stripe' => [
        'model' => App\Models\Tenant::class,   // since you’re Tenant-first
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
    ],

    'maps' => [
        'js_key' => env('GOOGLE_MAPS_API_KEY'),
        'backend_key' => env('GOOGLE_MAPS_BACKEND_KEY'),
        'map_id' => env('GOOGLE_MAPS_MAP_ID'), // optional: required for vector maps & Advanced Markers
    ],

    'iowa_sor' => [
        'base' => env('IOWA_SOR_BASE_URL', 'https://www.iowasexoffender.gov'),
        'timeout' => 10,
        'per_hour_limit' => 50,
    ],
];
