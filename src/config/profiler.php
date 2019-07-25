<?php
return [
    'default' => env('PROFILER_DRIVER', 'file'),
    'enable-db-profiler' => true,
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