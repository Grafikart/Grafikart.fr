<?php

namespace App\Infrastructure\Twitter;

class Tweet
{
    private array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function getContent(): string
    {
        return $this->data['text'];
    }
}
