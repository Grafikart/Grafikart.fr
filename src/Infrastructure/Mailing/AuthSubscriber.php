<?php

namespace App\Infrastructure\Mailing;

use App\Domain\Password\Event\PasswordResetTokenCreatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AuthSubscriber implements EventSubscriberInterface
{
    private Mailer $mailer;

    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * @return array<string,string>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            PasswordResetTokenCreatedEvent::class => 'onPasswordRequest',
        ];
    }

    public function onPasswordRequest(PasswordResetTokenCreatedEvent $event): void
    {
        $email = $this->mailer->createEmail('mails/auth/password_reset.twig', [
            'token' => $event->getToken()->getToken(),
            'id' => $event->getUser()->getId(),
            'username' => $event->getUser()->getUsername(),
        ])
            ->to($event->getUser()->getEmail())
            ->subject('Grafikart | Réinitialisation de votre mot de passe');
        $this->mailer->send($email);
    }
}
