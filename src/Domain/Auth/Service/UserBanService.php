<?php

namespace App\Domain\Auth\Service;

use App\Domain\Auth\Event\UserBannedEvent;
use App\Domain\Auth\User;
use Psr\EventDispatcher\EventDispatcherInterface;

class UserBanService
{
    private EventDispatcherInterface $dispatcher;

    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    public function ban(User $user): void
    {
        $user->setBannedAt(new \DateTime());
        $this->dispatcher->dispatch(new UserBannedEvent($user));
    }
}
