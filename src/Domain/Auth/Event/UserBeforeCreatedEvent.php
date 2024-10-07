<?php

namespace App\Domain\Auth\Event;

use App\Domain\Auth\User;
use Symfony\Component\HttpFoundation\Request;

class UserBeforeCreatedEvent
{
    public function __construct(
        public readonly User $user,
        public readonly Request $request,
    ) {
    }
}
