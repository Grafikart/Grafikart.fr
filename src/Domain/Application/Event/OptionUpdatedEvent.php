<?php

namespace App\Domain\Application\Event;

use App\Domain\Application\Entity\Option;

class OptionUpdatedEvent
{
    public function __construct(private readonly Option $option)
    {
    }

    public function getOption(): Option
    {
        return $this->option;
    }
}
