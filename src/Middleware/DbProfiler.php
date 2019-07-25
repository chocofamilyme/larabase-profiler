<?php

namespace Chocofamily\Profiler\Middleware;

use Closure;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Database\Events\StatementPrepared;
use Chocofamily\Profiler\ProfilerInterface as ProfilerInterface;

class DbProfiler
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

        Event::listen(StatementPrepared::class, function ($event) {
            $sql = $event->statement->queryString;

            $this->profiler->start([
                'group'    => 'database',
                'type'     => $this->getOperationType($sql),
                'query'    => $sql,
                'params'   => [],
            ]);
        });

        DB::listen(function (QueryExecuted $query) {
            /*
            $sql = $this->applyBindings($query->sql, $query->bindings);

            $this->profiler->start([
                'group'    => 'database',
                'type'     => $this->getOperationType($sql),
                'query'    => $sql,
                'params' => $query->bindings,
            ]);
            */
            $this->profiler->stop();
        });

        return $next($request);
    }

    /**
     * Включено ли профилирование запросов бд
     * @return bool
     */
    private function isEnabled(): bool
    {
        return ((bool) config('profiler.enable-db-profiler') && (bool) config('profiler.enable'));
    }

    /**
     * Подставляем в sql данные
     *
     * @param  string $sql
     * @param  array $bindings
     * @return string
     */
    private function applyBindings(string $sql, array $bindings): string
    {
        if (empty($bindings)) {
            return $sql;
        }
        foreach ($bindings as $binding) {
            switch (gettype($binding)) {
                case 'boolean':
                    $binding = (int) $binding;
                    break;
                case 'string':
                    $binding = "'{$binding}'";
                    break;
            }
            $sql = preg_replace('/\?/', $binding, $sql, 1);
        }
        return $sql;
    }

    /**
     * Получаем тип SQL запроса
     *
     * @param  string $sql
     * @return string
     */
    private function getOperationType(string $sql) : string
    {
        $operationType = 'OTHER';
        if (strpos($sql, 'INSERT') === 0) {
            $operationType = 'INSERT';
        } elseif (strpos($sql, 'UPDATE') === 0) {
            $operationType = 'UPDATE';
        } elseif (strpos($sql, 'SELECT') === 0) {
            $operationType = 'SELECT';
        } elseif (strpos($sql, 'DELETE') === 0) {
            $operationType = 'DELETE';
        }

        return $operationType;
    }
}