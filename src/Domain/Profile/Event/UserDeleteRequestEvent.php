<?php

namespace App\Domain\Profile\Event;

use App\Domain\Auth\User;

class UserDeleteRequestEvent
{

    /**
     * @var User
     */
    private User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function getUser(): User
    {
        return $this->user;
    }
}
