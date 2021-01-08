<?php

namespace App\Infrastructure\Queue;

use Symfony\Component\Messenger\Bridge\Doctrine\Transport\DoctrineReceivedStamp;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Stamp\ErrorDetailsStamp;
use Symfony\Component\Messenger\Stamp\RedeliveryStamp;

class FailedJob
{
    private Envelope $envelope;
    private string $id;

    public function __construct(Envelope $envelope)
    {
        $this->envelope = $envelope;

        // On obtient l'ID depuis le stamp doctrine
        /** @var ?DoctrineReceivedStamp $stamp */
        $stamp = $envelope->last(DoctrineReceivedStamp::class);
        if ($stamp) {
            $this->id = $stamp->getId();
        }
    }

    public function getId(): string
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
        /** @var ?ErrorDetailsStamp $stamp */
        $stamp = $this->envelope->last(ErrorDetailsStamp::class);

        return $stamp ? $stamp->getExceptionMessage() : '';
    }

    public function getFailedAt(): \DateTimeInterface
    {
        /** @var ?RedeliveryStamp $stamp */
        $stamp = $this->envelope->last(RedeliveryStamp::class);

        return $stamp ? $stamp->getRedeliveredAt() : new \DateTimeImmutable();
    }

    public function getTrace(): string
    {
        /** @var ?ErrorDetailsStamp $stamp */
        $stamp = $this->envelope->last(ErrorDetailsStamp::class);
        if (!$stamp) {
            return '';
        }
        $exception = $stamp->getFlattenException();

        return $exception ? $exception->getTraceAsString() : '';
    }
}
