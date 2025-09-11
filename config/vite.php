<?php

return [
    'dev_server' => [
        'url' => env('VITE_DEV_SERVER_URL', 'http://peaceproxy.test:5173'),
        'ping_timeout' => env('VITE_DEV_SERVER_PING_TIMEOUT', 300),
    ],
];
