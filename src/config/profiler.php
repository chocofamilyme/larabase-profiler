<?php
return [
    'driver' => env('PROFILER_DRIVER', 'file'),
    'enable' => env('PROFILER_ENABLE', false),
    'enable-db-profiler' => env('PROFILER_ENABLE_DB_PROFILER', false),
    'drivers' => [
        'pinba' => [
            'adapter' => 'Pinba',
            'host' => env('APP_DOMAIN', 'pinbadomain.dev'),
            'server' => env('PROFILER_SERVER', 'pinbadomain.dev')
        ],
        'file' => [
            'adapter' => 'File',
            'host' => env('APP_DOMAIN', 'filedomain.dev'),
            'server' => env('PROFILER_SERVER', 'pinbadomain.dev')
        ],
    ],
];