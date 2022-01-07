<?php

namespace App\Domain\Badge\Event;

use App\Domain\Auth\User;
use App\Domain\Badge\Entity\Badge;
use App\Domain\Badge\Entity\BadgeUnlock;

class BadgeUnlockEvent
{
    public function __construct(private BadgeUnlock $unlock)
    {
    }

    public function getBadge(): Badge
    {
        return $this->unlock->getBadge();
    }

    public function getUser(): User
    {
        return $this->unlock->getOwner();
    }
}
