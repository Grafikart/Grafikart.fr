<?php

namespace App\Tests\Http\Twig;

use App\Http\Twig\CacheExtension\CacheableInterface;

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
