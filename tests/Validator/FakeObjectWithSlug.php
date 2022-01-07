<?php

namespace App\Tests\Validator;

class FakeObjectWithSlug
{
    public function __construct(public string $slug, private readonly ?int $id = null)
    {
    }

    public function getId(): ?int
    {
        return $this->id;
    }
}
