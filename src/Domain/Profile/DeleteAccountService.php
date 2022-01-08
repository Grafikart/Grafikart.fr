<?php

namespace App\Domain\Profile;

use App\Domain\Auth\AuthService;
use App\Domain\Auth\User;
use App\Domain\Profile\Event\UserDeleteRequestEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;

class DeleteAccountService
{
    public const DAYS = 5;

    public function __construct(
        private EntityManagerInterface $em,
        private EventDispatcherInterface $dispatcher,
        private AuthService $authService
    ) {
    }

    public function deleteUser(User $user, Request $request): void
    {
        $this->authService->logout($request);
        $this->dispatcher->dispatch(new UserDeleteRequestEvent($user));
        $user->setDeleteAt(new \DateTimeImmutable('+ '.(string) self::DAYS.' days'));
        $this->em->flush();
    }
}
