<?php

namespace App\Domains\Notification;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class NotificationData extends Data
{
    public function __construct(
        public string $message,
        public string $url,
        #[MapInputName('created_at')]
        public \DateTimeInterface $date,
        public ?int $userId,
    ) {}

}
