<?php

namespace App\Infrastructure\Queue;

use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Transport\Receiver\ListableReceiverInterface;
use Symfony\Component\Messenger\Transport\Sync\SyncTransport;
use Symfony\Component\Messenger\Transport\TransportInterface;

class FailedJobsService
{
    private readonly ListableReceiverInterface $receiver;

    public function __construct(TransportInterface $receiver, private readonly MessageBusInterface $messageBus)
    {
        if (!($receiver instanceof ListableReceiverInterface)) {
            throw new \Exception('Le service '.self::class.' attend un receiver de type '.ListableReceiverInterface::class);
        }
        $this->receiver = $receiver;
    }

    /**
     * @return FailedJob[]
     */
    public function getJobs(): array
    {
        if ($this->receiver instanceof SyncTransport) {
            return [];
        }
        $envelopes = $this->receiver->all();
        if ($envelopes instanceof \Traversable) {
            $envelopes = iterator_to_array($envelopes);
        }

        return array_map(fn (Envelope $envelope) => new FailedJob($envelope), $envelopes);
    }

    public function retryJob(int $jobId): void
    {
        $envelope = $this->receiver->find($jobId);
        if ($envelope instanceof Envelope) {
            $this->messageBus->dispatch($envelope->getMessage());
            $this->receiver->reject($envelope);
        } else {
            throw new \RuntimeException("Impossible de trouver le job #{$jobId}");
        }
    }

    public function deleteJob(int $jobId): void
    {
        $envelope = $this->receiver->find($jobId);
        if ($envelope instanceof Envelope) {
            $this->receiver->reject($envelope);
        }
    }
}
