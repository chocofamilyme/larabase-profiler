<?php
return [
    'default' => env('PROFILER_DRIVER', 'file'),

    'drivers' => [
        'pinba' => [
            'adapter' => 'Pinba',
            'host' => env('APP_DOMAIN', 'chocodev.kz'),
        ],
        'file' => [
            'adapter' => 'File',
            'host' => env('APP_DOMAIN', 'chocodev.kz'),
        ],
    ],
];