<?php

namespace App\Tests\Domain\Notification;

class FakeEntity
{
    public function __construct(private readonly int $id)
    {
    }

    public function getId(): int
    {
        return $this->id;
    }
}
