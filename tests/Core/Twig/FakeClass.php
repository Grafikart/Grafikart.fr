<?php

namespace App\Tests\Core\Twig;

use App\Core\Twig\CacheExtension\CacheableInterface;

class FakeClass implements CacheableInterface
{
    public function getId(): int
    {
        return 4;
    }

    public function getUpdatedAt(): \DateTimeInterface
    {
        return new \DateTime('@12312312');
    }
}
