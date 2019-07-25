<?php

namespace Chocofamily\Profiler;

use Illuminate\Support\Facades\Log;

/**
 *
 * Профилирование в файл
 * Class File
 *
 * @package Chocofamily\Profiler
 */
class File implements ProfilerInterface
{
    private $logger;
    private $timers;
    private $incr = 0;

    /**
     * File constructor.
     *
     * @param array $config
     *
     * @throws \Exception
     */
    public function __construct(array $config)
    {}

    public function start(array $tags): int
    {
        $currentTags                         = $this->incr++;
        $this->timers[$currentTags]['tags']  = $tags;
        $this->timers[$currentTags]['start'] = microtime(true);

        return $currentTags;
    }

    public function stop(int $timerId = 0)
    {
        if ($timerId) {
            $currentTags = $timerId;
        } else {
            $currentTags = --$this->incr;
        }
        $this->timers[$currentTags]['stop'] = microtime(true);
        $time = $this->timers[$currentTags]['stop'] - $this->timers[$currentTags]['start'];
        Log::debug(print_r($this->timers[$currentTags]['tags'], true).' : '.$time.' секунд');
    }

    public function stopAll()
    {
        while ($this->incr > 0) {
            $this->stop();
        }
    }

    public function script(string $url)
    {
        Log::debug($url);
    }

    /**
     * @return mixed
     */
    public function getTimers()
    {
        return $this->timers;
    }

    /**
     * Вернет информацию по все таймерам и метрикам
     *
     * @return array
     */
    public function getData(): array
    {
        return $this->getTimers();
    }
}