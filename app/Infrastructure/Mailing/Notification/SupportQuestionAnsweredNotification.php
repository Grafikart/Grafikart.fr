<?php

namespace App\Infrastructure\Mailing\Notification;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SupportQuestionAnsweredNotification extends Notification
{
    public function __construct(
        private readonly string $course,
        private readonly string $url,
    ) {}

    public function via(): array
    {
        return ['mail'];
    }

    public function toMail(): MailMessage
    {
        return (new MailMessage)
            ->subject('Grafikart::Réponse à votre question')
            ->greeting('Votre question a reçu une réponse')
            ->line("Votre question sur le tutoriel **{$this->course}** a reçu une réponse.")
            ->action('Voir la réponse', $this->url);
    }
}
