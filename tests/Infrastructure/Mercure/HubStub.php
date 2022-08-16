<?php

namespace App\Tests\Infrastructure\Mercure;

use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Jwt\TokenFactoryInterface;
use Symfony\Component\Mercure\Jwt\TokenProviderInterface;
use Symfony\Component\Mercure\MockHub;
use Symfony\Component\Mercure\Update;

class HubStub implements HubInterface
{
    public static $lastUpdate = null;


    public function publish(Update $update): string
    {
        self::$lastUpdate = $update;

        return '';
    }

    public function getUrl(): string
    {
        return '';
    }

    public function getPublicUrl(): string
    {
        return '';
    }

    public function getProvider(): TokenProviderInterface
    {
        throw new \RuntimeException('Mock');
    }

    public function getFactory(): ?TokenFactoryInterface
    {
        return null;
    }
}
