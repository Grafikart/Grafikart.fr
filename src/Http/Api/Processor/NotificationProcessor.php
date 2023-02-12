<?php

namespace App\Http\Api\Processor;

use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Domain\Auth\User;
use App\Domain\Notification\Entity\Notification;
use Symfony\Bundle\SecurityBundle\Security;

final class NotificationProcessor implements ProcessorInterface
{
    public function __construct(
        private ProcessorInterface $persistProcessor,
        private ProcessorInterface $removeProcessor,
        private readonly Security $security
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        $user = $this->security->getUser();
        if (!$data instanceof Notification || !$user instanceof User) {
            return [];
        }
        if ($operation instanceof Delete) {
            return $this->removeProcessor->process($data, $operation, $uriVariables, $context);
        }

        $data->setUser($user);
        $result = $this->persistProcessor->process($data, $operation, $uriVariables, $context);

        return $result->setUser(null);
    }
}
