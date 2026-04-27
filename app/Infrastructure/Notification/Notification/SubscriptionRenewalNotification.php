<?php

namespace App\Infrastructure\Notification\Notification;

use App\Domains\Premium\Models\Subscription;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubscriptionRenewalNotification extends Notification
{
    public function __construct(
        private readonly Subscription $subscription,
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(): array
    {
        return ['mail'];
    }

    public function toMail(): MailMessage
    {
        $date = $this->subscription->next_payment?->format('d/m/Y') ?? '';

        return (new MailMessage)
            ->subject('Grafikart::Renouvellement prochain de votre abonnement')
            ->greeting('Bonjour,')
            ->line("Votre abonnement Premium à Grafikart.fr sera renouvelé le **{$date}**.")
            ->line('Si vous ne souhaitez pas renouveler votre abonnement, vous pouvez le résilier depuis votre espace personnel avant cette date.')
            ->action('Gérer mon abonnement', route('users.edit'))
            ->line('Merci de votre soutien !');
    }
}
