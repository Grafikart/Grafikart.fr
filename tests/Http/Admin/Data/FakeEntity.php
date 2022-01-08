<?php

namespace App\Tests\Http\Admin\Data;

class FakeEntity
{
    public function __construct(private string $name)
    {
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
