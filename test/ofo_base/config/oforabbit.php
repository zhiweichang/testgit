<?php
return [
    'ofo_base' => [
        'host' => env('RABBIT_BASE_HOST', '127.0.0.1'),
        'port' => env('RABBIT_BASE_PORT', 5672),
        'user' => env('RABBIT_BASE_USER', 'demo'),
        'password' => env('RABBIT_BASE_PASSWORD', 'demo'),
        'vhost' => env('RABBIT_BASE_VHOST', 'base'),
        'exchange' => env('RABBIT_BASE_EXCHANGE', 'base'),
        'type' => env('RABBIT_BASE_TYPE', 'topic'),
    ],
];
