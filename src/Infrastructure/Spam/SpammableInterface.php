<?php

namespace App\Infrastructure\Spam;

interface SpammableInterface
{
    public function isSpam(): bool;

    public function setSpam(bool $spam): object;
}
