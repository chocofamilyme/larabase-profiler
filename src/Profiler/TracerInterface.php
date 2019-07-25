<?php

namespace Chocofamily\Profiler;

interface TracerInterface
{
    public function getCorrelationId(): string;
    public function getSpanId(): int;
}