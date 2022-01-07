<?php

namespace App\Domain\Notification\Event;

use App\Domain\Notification\Entity\Notification;

class NotificationCreatedEvent
{
    public function __construct(private Notification $notification)
    {
    }

    public function getNotification(): Notification
    {
        return $this->notification;
    }
}
