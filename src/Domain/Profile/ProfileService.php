<?php

namespace App\Domain\Profile;

use App\Domain\Profile\Dto\AvatarDto;
use App\Domain\Profile\Dto\ProfileUpdateDto;
use App\Domain\Profile\Entity\EmailVerification;
use App\Domain\Profile\Event\EmailVerificationEvent;
use App\Domain\Profile\Exception\TooManyEmailChangeException;
use App\Domain\Profile\Repository\EmailVerificationRepository;
use App\Infrastructure\Security\TokenGeneratorService;
use Doctrine\ORM\EntityManagerInterface;
use Intervention\Image\ImageManager;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ProfileService
{
    private TokenGeneratorService $tokenGeneratorService;
    private EmailVerificationRepository $emailVerificationRepository;
    private EventDispatcherInterface $dispatcher;

    private EntityManagerInterface $em;

    public function __construct(
        TokenGeneratorService $tokenGeneratorService,
        EmailVerificationRepository $emailVerificationRepository,
        EventDispatcherInterface $dispatcher,
        EntityManagerInterface $em
    ) {
        $this->tokenGeneratorService = $tokenGeneratorService;
        $this->emailVerificationRepository = $emailVerificationRepository;
        $this->dispatcher = $dispatcher;
        $this->em = $em;
    }

    public function updateAvatar(AvatarDto $data): void
    {
        if (false === $data->file->getRealPath()) {
            throw new \RuntimeException('Impossible de redimensionner un avatar non existant');
        }
        // On redimensionne l'image
        $manager = new ImageManager(['driver' => 'imagick']);
        $manager->make($data->file)->fit(110, 110)->save($data->file->getRealPath());

        // On la déplace dans le profil utilisateur
        $data->user->setAvatarFile($data->file);
        $data->user->setUpdatedAt(new \DateTime());
    }

    public function updateProfile(ProfileUpdateDto $data): void
    {
        $data->user->setCountry($data->country);
        $data->user->setUsername($data->username);
        $data->user->setForumMailNotification($data->forumNotification);
        if (true === $data->useSystemTheme) {
            $data->user->setTheme(null);
        } else {
            $data->user->setTheme($data->useDarkTheme ? 'dark' : 'light');
        }
        if ($data->email !== $data->user->getEmail()) {
            $lastRequest = $this->emailVerificationRepository->findLastForUser($data->user);
            if ($lastRequest && $lastRequest->getCreatedAt() > new \DateTime('-1 hour')) {
                throw new TooManyEmailChangeException($lastRequest);
            } else {
                if ($lastRequest) {
                    $this->em->remove($lastRequest);
                }
            }
            $emailVerification = (new EmailVerification())
                ->setEmail($data->email)
                ->setAuthor($data->user)
                ->setCreatedAt(new \DateTime())
                ->setToken($this->tokenGeneratorService->generate());
            $this->em->persist($emailVerification);
            $this->dispatcher->dispatch(new EmailVerificationEvent($emailVerification));
        }
    }

    public function updateEmail(EmailVerification $emailVerification): void
    {
        $emailVerification->getAuthor()->setEmail($emailVerification->getEmail());
        $this->em->remove($emailVerification);
    }
}
