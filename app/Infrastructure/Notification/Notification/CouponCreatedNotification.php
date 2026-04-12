<?php

namespace App\Infrastructure\Notification\Notification;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CouponCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly string $subject,
        private readonly ?string $message,
        private readonly int $months,
        private readonly string $code,
    ) {
        $this->afterCommit();
    }

    public function via(): array
    {
        return ['mail'];
    }

    public function toMail(): MailMessage
    {
        return (new MailMessage)
            ->subject($this->subject)
            ->greeting($this->subject)
            ->lineIf(boolean: boolval($this->message), line: $this->message)
            ->line("Pour profiter de vos **{$this->months}** mois de compte premium vous pouvez utiliser le code suivant lors de votre inscription :")
            ->line(<<<MD
```
{$this->code}
```
MD
            )
            ->action('Créer mon compte sur Grafikart.fr', route('register', ['coupon' => $this->code]));
    }
}
