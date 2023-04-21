<?php

namespace App\Infrastructure\Queue;

use App\Infrastructure\Queue\Message\ServiceMethodMessage;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Stamp\DelayStamp;

class ScheduledJob
{
    public function __construct(private readonly Envelope $envelope, private readonly int $id)
    {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getEnvelope(): Envelope
    {
        return $this->envelope;
    }

    public function getMessageClass(): string
    {
        return $this->envelope->getMessage()::class;
    }

    public function getMessage(): object
    {
        return $this->envelope->getMessage();
    }

    public function getDescription(): string
    {
        $message = $this->getMessage();
        if ($message instanceof ServiceMethodMessage) {
            $params = array_map(function (mixed $item) {
                if (is_object($item)) {
                    return $item::class;
                }

                return $item;
            }, $message->getParams());
            $method = $message->getMethod();

            return sprintf('%s(%s)', $method, json_encode($params));
        }

        return '';
    }

    public function getPublishDate(): \DateTimeInterface
    {
        $delay = $this->envelope->last(DelayStamp::class);
        if (!$delay) {
            return new \DateTimeImmutable();
        }
        $delaySeconds = $delay->getDelay() / 1000;

        return (new \DateTimeImmutable())->add(new \DateInterval("PT{$delaySeconds}S"));
    }
}
