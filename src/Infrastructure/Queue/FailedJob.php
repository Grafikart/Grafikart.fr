<?php

namespace App\Infrastructure\Queue;

use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Stamp\RedeliveryStamp;

class FailedJob
{
    private RedeliveryStamp $lastStamp;
    private Envelope $envelope;
    private int $id;

    public function __construct(Envelope $envelope, int $id)
    {
        $this->lastStamp = $this->getLastRedeliveryStampWithException($envelope);
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

    public function getMessage(): object
    {
        return $this->envelope->getMessage();
    }

    public function getMessageClass(): string
    {
        return get_class($this->getMessage());
    }

    public function getErrorMessage(): string
    {
        return $this->lastStamp->getExceptionMessage() ?: '';
    }

    public function getFailedAt(): \DateTimeInterface
    {
        return $this->lastStamp->getRedeliveredAt();
    }

    public function getTrace(): string
    {
        $exception = $this->lastStamp->getFlattenException();

        return $exception ? $exception->getTraceAsString() : '';
    }

    private function getLastRedeliveryStampWithException(Envelope $envelope): RedeliveryStamp
    {
        /** @var RedeliveryStamp $stamp */
        foreach (array_reverse($envelope->all(RedeliveryStamp::class)) as $stamp) {
            if (null !== $stamp->getExceptionMessage()) {
                return $stamp;
            }
        }
        throw new \RuntimeException('No exception for the job');
    }
}
