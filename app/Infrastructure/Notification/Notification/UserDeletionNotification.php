<?php

namespace App\Infrastructure\Notification\Notification;

use App\Models\User;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserDeletionNotification extends Notification
{
    public function __construct(
        private readonly User $user,
        private readonly string $reason,
    ) {}

    public function via(): array
    {
        return ['mail'];
    }

    public function toMail(): MailMessage
    {
        return (new MailMessage)
            ->subject("Grafikart::Suppression de compte : {$this->user->name}")
            ->greeting('Suppression de compte')
            ->line("**{$this->user->email}** a demandé la suppression de son compte pour la raison suivante :")
            ->line($this->reason);
    }
}
