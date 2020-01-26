<?php

namespace App\Domain\Live;

class LiveCreatedEvent
{

    private Live $live;

    public function __construct(Live $live)
    {
        $this->live = $live;
    }

    public function getLive(): Live
    {
        return $this->live;
    }

}
