<?php

namespace App\Infrastructure\Notification\Channel;

/**
 * Interface to indicate a Notification handle site Notifications
 */
interface HasSiteNotification
{
    public function toSiteNotification(object $notifiable): SiteNotificationMessage;
}
