<?php

namespace App\Infrastructure\Notification\Channel;

/**
 * Message send to the channel to distribute site notification
 */
readonly class SiteNotificationMessage
{
    public function __construct(
        public string $url,
        public string $message,
        public mixed $target = null,
    ) {}
}
