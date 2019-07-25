<?php

namespace Chocofamily\Profiler\Middleware;

use Closure;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Database\Events\StatementPrepared;
use Chocofamily\Profiler\ProfilerInterface as ProfilerInterface;

class Profiler
{
    /* @var \Chocofamily\Profiler\ProfilerInterface $profiler*/
    private $profiler;

    /**
     * Handle an incoming request.
     *
     * @param \Chocofamily\Profiler\ProfilerInterface $profiler
     */
    public function __construct(ProfilerInterface $profiler)
    {
        $this->profiler = $profiler;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle(\Illuminate\Http\Request $request, Closure $next)
    {
        if (!$this->isEnabled()) {
            return $next($request);
        }

        $url = $request->fullUrl();
        $method = $request->method();

        $this->profiler->script($method.': '.$url);

        return $next($request);
    }

    /**
     * Включено ли профилирование
     * @return bool
     */
    private function isEnabled(): bool
    {
        return (bool) config('profiler.enable');
    }
}