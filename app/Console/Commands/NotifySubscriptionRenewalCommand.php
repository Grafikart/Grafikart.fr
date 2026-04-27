<?php

namespace App\Console\Commands;

use App\Domains\Premium\Models\Subscription;
use App\Infrastructure\Notification\Notification\SubscriptionRenewalNotification;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\Isolatable;

class NotifySubscriptionRenewalCommand extends Command implements Isolatable
{
    protected $signature = 'app:notify-subscription-renewal';

    protected $description = 'Notify users 5 days before their subscription renewal (once per subscription)';

    public function handle(): int
    {
        $subscriptions = Subscription::query()
            ->where('state', Subscription::ACTIVE)
            ->whereNull('notified_at')
            ->whereBetween('next_payment', [now(), now()->addDays(5)])
            ->with('user')
            ->get();

        $count = 0;
        foreach ($subscriptions as $subscription) {
            if ($subscription->user === null) {
                continue;
            }
            $subscription->user->notify(new SubscriptionRenewalNotification($subscription));
            $subscription->update(['notified_at' => now()]);
            $count++;
        }

        $this->info("Sent {$count} renewal notice(s).");

        return self::SUCCESS;
    }
}
