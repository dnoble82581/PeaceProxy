<?php

return [
    'plans' => [
        'team_monthly' => env('STRIPE_PRICE_TEAM_MONTHLY'),
        'team_yearly'  => env('STRIPE_PRICE_TEAM_YEARLY'),
    ],
    'matrix' => [
        env('STRIPE_PRICE_TEAM_MONTHLY') => [
            'incidents.unlimited',
            'chat.reverb',
            'reports.export.pdf',
        ],
        env('STRIPE_PRICE_TEAM_YEARLY') => [
            'incidents.unlimited',
            'chat.reverb',
            'reports.export.pdf',
            'support.priority',
        ],
    ],
];
