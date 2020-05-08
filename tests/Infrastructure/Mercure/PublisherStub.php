<?php

namespace App\Tests\Infrastructure\Mercure;

use Symfony\Component\Mercure\PublisherInterface;
use Symfony\Component\Mercure\Update;

class PublisherStub implements PublisherInterface
{
    public static $lastUpdate = null;

    public function __invoke(Update $update): string
    {
        self::$lastUpdate = $update;
        return '';
    }

}
