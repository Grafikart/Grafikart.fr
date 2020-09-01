<?php

namespace App\Domain\Profile;

use App\Domain\Auth\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Http\Event\LogoutEvent;

class DeleteAccountService
{

    public const DAYS = 5;
    private EntityManagerInterface $em;
    private TokenStorageInterface $tokenStorage;
    private EventDispatcherInterface $dispatcher;

    public function __construct(
        EntityManagerInterface $em,
        EventDispatcherInterface $dispatcher,
        TokenStorageInterface $tokenStorage
    ) {
        $this->em = $em;
        $this->dispatcher = $dispatcher;
        $this->tokenStorage = $tokenStorage;
    }

    public function deleteUser(User $user, Request $request): void
    {
        $this->dispatcher->dispatch(new LogoutEvent($request, $this->tokenStorage->getToken()));
        $this->tokenStorage->setToken(null);
        $user->setDeleteAt(new \DateTime());
        $this->em->flush();
    }
}
