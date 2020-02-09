<?php

namespace App\Twig\CacheExtension;

interface CacheableInterface
{

    public function getUpdatedAt(): \DateTimeInterface;

    public function getId(): int;

}
