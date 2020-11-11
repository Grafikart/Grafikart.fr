<?php

namespace App\Infrastructure\Queue;

use App\Infrastructure\Queue\Message\ServiceMethodMessage;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Stamp\DelayStamp;

class ScheduledJob
{
    private Envelope $envelope;
    private int $id;

    public function __construct(Envelope $envelope, int $id)
    {
        $this->envelope = $envelope;
        $this->id = $id;
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
        return get_class($this->envelope->getMessage());
    }

    public function getMessage(): object
    {
        return $this->envelope->getMessage();
    }

    public function getParams(): array
    {
        $message = $this->getMessage();
        if ($message instanceof ServiceMethodMessage) {
            return $message->getParams();
        }

        return [];
    }

    public function getPublishDate(): \DateTimeInterface
    {
        /** @var DelayStamp $delay */
        $delay = $this->envelope->last(DelayStamp::class);
        $delaySeconds = $delay->getDelay() / 1000;

        return (new \DateTimeImmutable())->add(new \DateInterval("PT{$delaySeconds}S"));
    }
}
