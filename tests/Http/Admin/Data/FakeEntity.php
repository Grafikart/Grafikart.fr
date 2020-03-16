<?php

namespace App\Tests\Http\Admin\Data;

class FakeEntity
{

    private string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function setName (string $name): void {
        $this->name = $name;
    }

    public function getName(): string {
        return $this->name;
    }

}
