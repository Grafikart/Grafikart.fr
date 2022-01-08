<?php

namespace App\Domain\Auth\Service;

use App\Domain\Auth\Event\UserBannedEvent;
use App\Domain\Auth\User;
use Psr\EventDispatcher\EventDispatcherInterface;

class UserBanService
{
    public function __construct(private readonly EventDispatcherInterface $dispatcher)
    {
    }

    public function ban(User $user): void
    {
        $user->setBannedAt(new \DateTime());
        $this->dispatcher->dispatch(new UserBannedEvent($user));
    }
}
