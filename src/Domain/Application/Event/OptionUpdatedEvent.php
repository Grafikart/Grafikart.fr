<?php

namespace App\Domain\Application\Event;

use App\Domain\Application\Entity\Option;

class OptionUpdatedEvent
{
    private Option $option;

    public function __construct(Option $option)
    {
        $this->option = $option;
    }

    public function getOption(): Option
    {
        return $this->option;
    }
}
