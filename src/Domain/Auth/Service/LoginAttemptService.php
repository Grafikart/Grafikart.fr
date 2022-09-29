<?php

namespace App\Domain\Auth\Service;

use App\Domain\Auth\Entity\LoginAttempt;
use App\Domain\Auth\Repository\LoginAttemptRepository;
use App\Domain\Auth\User;
use Doctrine\ORM\EntityManagerInterface;

class LoginAttemptService
{
    final public const ATTEMPTS = 3;

    public function __construct(private readonly LoginAttemptRepository $repository, private readonly EntityManagerInterface $em)
    {
    }

    public function addAttempt(User $user): void
    {
        // TODO : Envoyer un email au bout du Xème essai
        $attempt = (new LoginAttempt())->setUser($user);
        $this->em->persist($attempt);
        $this->em->flush();
    }

    public function limitReachedFor(User $user): bool
    {
        return $this->repository->countRecentFor($user, 30) >= self::ATTEMPTS;
    }
}
