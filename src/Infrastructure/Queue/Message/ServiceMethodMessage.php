<?php

namespace App\Infrastructure\Queue\Message;

class ServiceMethodMessage
{
    public function __construct(
        private readonly string $serviceName,
        private readonly string $method,
        private readonly array $params = [],
    ) {
    }

    public function getServiceName(): string
    {
        return $this->serviceName;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getParams(): array
    {
        return $this->params;
    }
}
