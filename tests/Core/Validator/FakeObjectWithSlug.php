<?php


namespace App\Tests\Core\Validator;


class FakeObjectWithSlug
{

    public string $slug;
    private ?int $id;

    public function __construct(string $slug, ?int $id = null)
    {
        $this->slug = $slug;
        $this->id = $id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

}
