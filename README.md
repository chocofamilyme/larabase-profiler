# Laravel - профилирование запросов 

Библиотека для профилирования запросов. Может отправлять данные профилирования на сервер pinba или в файл. 

**Установка**

Установка с помощью композера
```bash
    composer require chocofamilyme/larabase-profiler
```

Для создания конфига введите в консоли команду

```bash
    php artisan vendor:publish
```

И выбирать пункт ``Tag: config``

**Инициализация**

В конфиг файле config/profiler.php нужно прописать настройки профайлера:
````php
  
    return [
        'driver' => env('PROFILER_DRIVER', 'pinba'),
        'enable' => true,
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
````

Теперь в нужном месте можно отправлять данные для профилирования в Pinba:

````php
$profiler = resolve('\Chocofamily\Profiler\ProfilerInterface');
$timer = $this->profiler->start([
    'group'    => 'database',
    'type'     => 'SELECT',
    'query'    => 'SELECT * FROM tags',
    'params' => [],
]);

// Какая-та логика приложения

$profiler->stop($timer);
````
