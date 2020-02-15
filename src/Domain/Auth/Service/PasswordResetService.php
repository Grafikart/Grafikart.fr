<?php

namespace App\Domain\Auth\Service;

use App\Domain\Auth\Data\PasswordResetRequestData;
use App\Domain\Auth\Entity\PasswordResetToken;
use App\Domain\Auth\Event\PasswordResetTokenCreatedEvent;
use App\Domain\Auth\Event\PasswordUpdatedEvent;
use App\Domain\Auth\Exception\OngoingPasswordResetException;
use App\Domain\Auth\Exception\UserNotFoundException;
use App\Domain\Auth\Repository\PasswordResetTokenRepository;
use App\Domain\Auth\User;
use App\Domain\Auth\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class PasswordResetService
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
     * @throws OngoingPasswordResetException
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

    public function updatePassword(string $password, User $user): void
    {
        $user->setPassword($this->encoder->encodePassword($user, $password));
        $this->em->flush();
        $this->dispatcher->dispatch(new PasswordUpdatedEvent($user));
    }

}
