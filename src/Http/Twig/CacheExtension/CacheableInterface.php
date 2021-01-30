<?php

namespace App\Http\Twig\CacheExtension;

interface CacheableInterface
{
    public function getUpdatedAt(): ?\DateTimeInterface;

    public function getId(): ?int;
}
