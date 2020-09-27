<?php

namespace App\Infrastructure\Queue;

use App\Infrastructure\Queue\Message\ServiceMethodMessage;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * Permet de demande l'exécution d'une méthode d'un service de manière asynchrone.
 */
class EnqueueMethod
{
    private MessageBusInterface $bus;

    public function __construct(MessageBusInterface $bus)
    {
        $this->bus = $bus;
    }

    public function enqueue(string $service, string $method, array $params = []): void
    {
        $this->bus->dispatch(
            new ServiceMethodMessage($service, $method, $params)
        );
    }
}
