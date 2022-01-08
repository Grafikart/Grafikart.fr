<?php

namespace App\Domain\Live;

class LiveCreatedEvent
{
    public function __construct(private readonly Live $live)
    {
    }

    public function getLive(): Live
    {
        return $this->live;
    }
}
