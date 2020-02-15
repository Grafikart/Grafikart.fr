<?php

namespace App\Domain\Auth\Service;

use App\Domain\Auth\Data\PasswordResetRequestData;
use App\Domain\Auth\Entity\PasswordResetToken;
use App\Domain\Auth\Event\PasswordResetRequestEvent;
use App\Domain\Auth\Exception\UserNotFoundException;
use App\Domain\Auth\Repository\PasswordResetTokenRepository;
use App\Domain\Auth\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class PasswordResetService
{

    private UserRepository $userRepository;
    private PasswordResetTokenRepository $tokenRepository;
    private EntityManagerInterface $em;
    private TokenGeneratorService $generator;
    private EventDispatcherInterface $dispatcher;

    public function __construct(
        UserRepository $userRepository,
        PasswordResetTokenRepository $tokenRepository,
        TokenGeneratorService $generator,
        EntityManagerInterface $em,
        EventDispatcherInterface $dispatcher
    )
    {

        $this->userRepository = $userRepository;
        $this->tokenRepository = $tokenRepository;
        $this->em = $em;
        $this->generator = $generator;
        $this->dispatcher = $dispatcher;
    }

    public function resetPassword(PasswordResetRequestData $data): void
    {
        // TODO: Améliorer cette méthode
        $users = $this->userRepository->findForAuth(['email' => $data->getEmail()]);
        if (empty($users)) {
            throw new UserNotFoundException();
        }
        $user = $users[0];
        // TODO: Laisser la possibiliter de renvoyer une instruction si la précédente à plus de 30 minutes
        $token = $this->tokenRepository->findBy(['user' => $user]);
        if (empty($token)) {
            $token = (new PasswordResetToken())
                ->setUser($user)
                ->setCreatedAt(new \DateTime())
                ->setToken($this->generator->generate());
            $this->em->persist($token);
            $this->em->flush();
            $this->dispatcher->dispatch(new PasswordResetRequestEvent($user, $token));
        }
    }

}
