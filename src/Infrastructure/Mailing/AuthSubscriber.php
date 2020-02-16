<?php

namespace App\Infrastructure\Mailing;

use App\Domain\Password\Event\PasswordResetTokenCreatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class AuthSubscriber implements EventSubscriberInterface
{

    private EmailFactory $factory;
    private MailerInterface $mailer;

    public function __construct(EmailFactory $factory, MailerInterface $mailer)
    {
        $this->factory = $factory;
        $this->mailer = $mailer;
    }

    /**
     * @return array<string,string>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            PasswordResetTokenCreatedEvent::class => 'onPasswordRequest'
        ];
    }

    public function onPasswordRequest(PasswordResetTokenCreatedEvent $event): void
    {
        $email = $this->factory->makeFromTemplate('mails/auth/password_reset.html.twig', [
            'token' => $event->getToken()->getToken(),
            'id' => $event->getUser()->getId(),
            'username' => $event->getUser()->getUsername()
        ])
            ->to($event->getUser()->getEmail())
            ->from('noreply@grafikart.fr')
            ->priority(Email::PRIORITY_HIGH)
            ->subject('Réinitialisation de votre mot de passe');
        $this->mailer->send($email);
    }
}
