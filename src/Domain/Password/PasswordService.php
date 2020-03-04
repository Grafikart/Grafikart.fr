<?php

namespace App\Domain\Password;

use App\Domain\Auth\Exception\UserNotFoundException;
use App\Domain\Auth\User;
use App\Domain\Auth\UserRepository;
use App\Domain\Password\Data\PasswordResetRequestData;
use App\Domain\Password\Entity\PasswordResetToken;
use App\Domain\Password\Event\PasswordRecoveredEvent;
use App\Domain\Password\Event\PasswordResetTokenCreatedEvent;
use App\Domain\Password\Exception\OngoingPasswordResetException;
use App\Domain\Password\Repository\PasswordResetTokenRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class PasswordService
{

    const EXPIRE_IN = 30; // Temps d'expiration d'un token

    private UserRepository $userRepository;
    private PasswordResetTokenRepository $tokenRepository;
    private EntityManagerInterface $em;
    private TokenGeneratorService $generator;
    private EventDispatcherInterface $dispatcher;
    private UserPasswordEncoderInterface $encoder;

    public function __construct(
        UserRepository $userRepository,
        PasswordResetTokenRepository $tokenRepository,
        TokenGeneratorService $generator,
        EntityManagerInterface $em,
        EventDispatcherInterface $dispatcher,
        UserPasswordEncoderInterface $encoder
    )
    {

        $this->userRepository = $userRepository;
        $this->tokenRepository = $tokenRepository;
        $this->em = $em;
        $this->generator = $generator;
        $this->dispatcher = $dispatcher;
        $this->encoder = $encoder;
    }

    /**
     * Lance une demande de réinitialisation de mot de passe
     * @throws \App\Domain\Password\Exception\OngoingPasswordResetException
     * @throws UserNotFoundException
     */
    public function resetPassword(PasswordResetRequestData $data): void
    {
        $user = $this->userRepository->findOneBy(['email' => $data->getEmail()]);
        if ($user === null) {
            throw new UserNotFoundException();
        }
        $token = $this->tokenRepository->findOneBy(['user' => $user]);
        if ($token !== null && !$this->isExpired($token)) {
            throw new OngoingPasswordResetException();
        }
        if ($token === null) {
            $token = new PasswordResetToken();
            $this->em->persist($token);
        }
        $token->setUser($user)
            ->setCreatedAt(new \DateTime())
            ->setToken($this->generator->generate());
        $this->em->flush();
        $this->dispatcher->dispatch(new PasswordResetTokenCreatedEvent($token));
    }

    public function isExpired(PasswordResetToken $token): bool
    {
        $expirationDate = new \DateTime('-' . self::EXPIRE_IN . ' minutes');
        return $token->getCreatedAt() < $expirationDate;
    }

    public function updatePassword(string $password, PasswordResetToken $token): void
    {
        $user = $token->getUser();
        $user->setPassword($this->encoder->encodePassword($user, $password));
        $this->em->remove($token);
        $this->em->flush();
        $this->dispatcher->dispatch(new PasswordRecoveredEvent($user));
    }

}
