<?php

namespace App\Infrastructure\Queue;

use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Transport\Receiver\ListableReceiverInterface;
use Symfony\Component\Messenger\Transport\Sync\SyncTransport;
use Symfony\Component\Messenger\Transport\TransportInterface;
use Traversable;

class FailedJobsService
{
    private ListableReceiverInterface $receiver;
    private MessageBusInterface $messageBus;

    public function __construct(TransportInterface $receiver, MessageBusInterface $messageBus)
    {
        if (!($receiver instanceof ListableReceiverInterface)) {
            throw new \Exception('Le service '.self::class.' attend un receiver de type '.ListableReceiverInterface::class);
        }
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
        $envelopes = $this->receiver->all();
        if ($envelopes instanceof Traversable) {
            $envelopes = iterator_to_array($envelopes);
        }

        return array_map(fn (Envelope $enveloppe) => new FailedJob($enveloppe), $envelopes);
    }

    public function retryJob(int $jobId): void
    {
        $enveloppe = $this->receiver->find($jobId);
        if ($enveloppe instanceof Envelope) {
            $this->messageBus->dispatch($enveloppe->getMessage());
            $this->receiver->reject($enveloppe);
        } else {
            throw new \RuntimeException("Impossible de trouver le job #{$jobId}");
        }
    }

    public function deleteJob(int $jobId): void
    {
        $envelope = $this->receiver->find($jobId);
        if ($envelope) {
            $this->receiver->reject($envelope);
        }
    }
}
