<?php

namespace App\Infrastructure\Twitter;

class Tweet
{
    public function __construct(private readonly array $data)
    {
    }

    public function getContent(): string
    {
        return $this->data['text'];
    }
}
