<?php

namespace Chocofamily\Profiler;

use Chocofamily\Pathcorrelation\Http\CorrelationId;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Chocofamily\Profiler\Middleware\Profiler as MiddlewareProfiler;
use Chocofamily\Profiler\Middleware\DbProfiler as MiddlewareDbProfiler;
use Chocofamily\Profiler\ProfilerInterface as ProfilerInterface;

class ProfilerServiceProvider extends BaseServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom($this->getPackageConfigPath(), 'profiler');
    }

    /**
     *
     */
    public function boot()
    {
        $this->publishConfig();

        /** @var \Illuminate\Config\Repository $config */

        $this->app->bind(ProfilerInterface::class, function ($app) {
            $config = $this->app['config'];

            $driver = strtolower($config->get('profiler.driver'));
            $adapter = 'Chocofamily\Profiler\\'.$config->get('profiler.drivers.' . $driver . '.adapter');
            $host   = $config->get('profiler.drivers.' . $driver . '.domain');
            $server = $config->get('profiler.drivers.' . $driver . '.server');

            return new $adapter(['host' => $host, 'server' => $server]);
        });

        $this->registerMiddleware(MiddlewareProfiler::class);
        $this->registerMiddleware(MiddlewareDbProfiler::class);
    }

    /**
     * Get the config path
     *
     * @return string
     */
    protected function getConfigPath()
    {
        return config_path('profiler.php');
    }

    /**
     * Get the config path
     *
     * @return string
     */
    protected function getPackageConfigPath()
    {
        return __DIR__ . '/config/profiler.php';
    }

    /**
     * Publish the config file
     *
     */
    protected function publishConfig()
    {
        $this->publishes([$this->getPackageConfigPath() => $this->getConfigPath()], 'config');
    }

    /**
     * Register the Middleware
     *
     * @param  string $middleware
     */
    protected function registerMiddleware($middleware)
    {
        $kernel = $this->app[Kernel::class];
        $kernel->pushMiddleware($middleware);
    }
}
