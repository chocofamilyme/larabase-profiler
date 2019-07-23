<?php

namespace Chocofamily\Profiler;

use Chocofamily\Pathcorrelation\Http\CorrelationId;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

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
        $this->mergeConfigFrom( __DIR__.'/config/config.php', 'profiler');
    }

    /**
     *
     */
    public function boot()
    {
        $this->publishConfig();

        /** @var \Illuminate\Config\Repository $config */
        $config = $this->app['config'];

        $adapter = 'Chocofamily\Profiler\\'.$config->get('drivers.' . $config->get('default') . '.adapter');
        $this->app->bind($adapter, function ($app, $config, $adapter) {
            $host   = $config->get('drivers.' . $config->get('default') . '.domain');
            $server = $config->get('drivers.' . $config->get('default') . '.server');

            return new $adapter(['host' => $host, 'server' => $server]);
        });
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
        return __DIR__ . '/../config/profiler.php';
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