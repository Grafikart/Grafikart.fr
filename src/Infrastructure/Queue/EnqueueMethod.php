<?php

namespace App\Infrastructure\Queue;

use App\Infrastructure\Queue\Message\ServiceMethodMessage;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;

/**
 * Permet de demande l'exécution d'une méthode d'un service de manière asynchrone.
 */
class EnqueueMethod
{
    public function __construct(private readonly MessageBusInterface $bus)
    {
    }

    public function enqueue(string $service, string $method, array $params = [], ?\DateTimeInterface $date = null): void
    {
        $stamps = [];
        // Le service doit être appelé avec un délai
        if (null !== $date) {
            $delay = 1000 * ($date->getTimestamp() - time());
            if ($delay > 0) {
                $stamps[] = new DelayStamp($delay);
            }
        }
        $this->bus->dispatch(new ServiceMethodMessage($service, $method, $params), $stamps);
    }
}
