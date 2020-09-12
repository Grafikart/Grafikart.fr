<?php

namespace App\Infrastructure\Queue;

use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Transport\Sync\SyncTransport;
use Symfony\Component\Messenger\Transport\TransportInterface;

class FailedJobsService
{
    private TransportInterface $receiver;
    private MessageBusInterface $messageBus;

    public function __construct(TransportInterface $receiver, MessageBusInterface $messageBus)
    {
        $this->receiver = $receiver;
        $this->messageBus = $messageBus;
    }

    /**
     * @return FailedJob[]
     */
    public function getJobs(): array
    {
        if ($this->receiver instanceof SyncTransport) {
            return [];
        }
        $envelopes = (array) $this->receiver->get();

        return array_map(fn (int $index) => new FailedJob($envelopes[$index], $index), array_keys($envelopes));
    }

    public function retryJob(int $jobId): void
    {
        $enveloppes = (array) $this->receiver->get();
        $enveloppe = $enveloppes[$jobId] ?? null;
        if ($enveloppe instanceof Envelope) {
            $this->messageBus->dispatch($enveloppe->getMessage());
            $this->receiver->reject($enveloppe);
        } else {
            throw new \RuntimeException("Impossible de trouver le job #{$jobId}");
        }
    }

    public function deleteJob(int $jobId): void
    {
        $enveloppes = (array) $this->receiver->get();
        $this->receiver->reject($enveloppes[$jobId]);
    }
}
