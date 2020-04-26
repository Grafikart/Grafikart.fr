<?php

namespace App\Infrastructure\Mailing;

use App\Domain\Profile\Event\EmailVerificationEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class ProfileSubscriber implements EventSubscriberInterface
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
            EmailVerificationEvent::class => 'onEmailChange'
        ];
    }

    public function onEmailChange(EmailVerificationEvent $event): void
    {
        // On envoie un email pour confirmer le compte
        $email = $this->factory->makeFromTemplate('mails/profile/email-confirmation.twig', [
            'token' => $event->emailVerification->getToken(),
            'username' => $event->emailVerification->getAuthor()->getUsername()
        ])
            ->to($event->emailVerification->getEmail())
            ->priority(Email::PRIORITY_HIGH)
            ->subject("Mise à jour de votre adresse mail");
        $this->mailer->send($email);

        // On notifie l'utilisateur concernant le changement
        $email = $this->factory->makeFromTemplate('mails/profile/email-notification.twig', [
            'username' => $event->emailVerification->getAuthor()->getUsername(),
            'email' => $event->emailVerification->getEmail()
        ])
            ->to($event->emailVerification->getAuthor()->getEmail())
            ->subject("Demande de changement d'email en attente");
        $this->mailer->send($email);
    }
}
