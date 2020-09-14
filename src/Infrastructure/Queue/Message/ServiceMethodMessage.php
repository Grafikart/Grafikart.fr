<?php

namespace App\Infrastructure\Queue\Message;

class ServiceMethodMessage
{
    private string $serviceName;
    private string $method;
    private array $params;

    public function __construct(string $serviceName, string $method, array $params = [])
    {
        $this->serviceName = $serviceName;
        $this->method = $method;
        $this->params = $params;
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
