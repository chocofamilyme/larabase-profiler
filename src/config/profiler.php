<?php
return [
    'default' => env('PROFILER_DRIVER', 'file'),

    'drivers' => [
        'pinba' => [
            'adapter' => 'Pinba',
            'host' => env('APP_DOMAIN', 'pinbadomain.dev'),
        ],
        'file' => [
            'adapter' => 'File',
            'host' => env('APP_DOMAIN', 'filedomain.dev'),
        ],
    ],
];