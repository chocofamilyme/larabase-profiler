<?php

namespace Chocofamily\Profiler;

/**
 * Класс Pinba обеспечивает простой интерфейс, который позволяет создавать произвольные пакеты данных Pinba в PHP.
 */
class Pinba implements ProfilerInterface
{
    /**
     * @var bool
     */
    private $isPinbaInstalled = true;

    /**
     * @var array
     */
    private $config;

    /**
     * @var array
     */
    private $initTags = [];

    /**
     * @var
     */
    private $timers;

    /**
     * @var int
     */
    private $incr = 0;

    /**
     * @var TracerInterface
     */
    private $tracer;

    /**
     * Pinba constructor.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        if (extension_loaded('pinba') == false) {
            $this->isPinbaInstalled = false;
        }

        $this->config = $config;
        $this->init();
        $this->setInitTags();
    }

    protected function init()
    {
        if ($this->isPinbaInstalled) {
            $hostName = (!isset($this->config['hostName']) ? $this->config['hostName'] : gethostname());
            $serverName = (!isset($this->config['serverName']) ? $this->config['serverName'] : ($_SERVER['SERVER_NAME'] ?? ''));
            $configSchema = (!isset($this->config['schema']) ? $this->config['schema'] : '');

            pinba_hostname_set($hostName);
            pinba_server_name_set($serverName);

            if ($schema = $configSchema) {
                pinba_schema_set($schema);
            }
        }
    }
    /**
     * Создает и запускает новый Таймер.
     *
     * @param $tags      - теги массив тегов и их значений в виде "тег" => "значение". Не может содержать числовые
     *                   показатели по понятным причинам.
     *
     * @return mixed
     */
    public function start(array $tags): int
    {
        if (!$this->isPinbaInstalled) {
            return 0;
        }
        if ($initTags = $this->getInitTags()) {
            $tags = array_merge($initTags, $tags);
        }
        $timerId                = $this->incr++;
        $this->timers[$timerId] = pinba_timer_start($tags);
        return $timerId;
    }
    /**
     * Останавливает таймер
     *
     * @param int $timerId
     */
    public function stop(int $timerId)
    {
        if (!$this->isPinbaInstalled) {
            return;
        }
        if ($this->isPinbaInstalled && isset($this->timers[$timerId])) {
            pinba_timer_stop($this->timers[$timerId]);
            unset($this->timers[$timerId]);
        }
    }
    public function stopAll()
    {
        if (!$this->isPinbaInstalled) {
            return;
        }
        pinba_timers_stop();
        unset($this->timers);
    }
    /**
     * Установить имя скрипта.
     *
     * @param $request_uri
     */
    public function script(string $request_uri)
    {
        if (!$this->isPinbaInstalled) {
            return;
        }
        pinba_script_name_set($request_uri);
    }
    /**
     * Вернет информацию по все таймерам и метрикам
     *
     * @return array
     */
    public function getData(): array
    {
        if (!$this->isPinbaInstalled) {
            return [];
        }
        return (array) pinba_get_info();
    }
    public function setInitTags()
    {
        if ($this->tracer) {
            $this->initTags = [
                'correlation_id' => $this->tracer->getCorrelationId(),
                'span_id'        => $this->tracer->getSpanId(),
            ];
        }
    }
    public function getInitTags()
    {
        return $this->initTags;
    }
    /**
     * @return mixed
     */
    public function getTimers()
    {
        return $this->timers;
    }
    /**
     * @return mixed
     */
    public function getTracer()
    {
        return $this->tracer;
    }
    /**
     * @param mixed $tracer
     */
    public function setTracer(TracerInterface $tracer)
    {
        $this->tracer = $tracer;
    }
}