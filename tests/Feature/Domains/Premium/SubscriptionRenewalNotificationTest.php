<?php

use App\Domains\Premium\Models\Subscription;
use App\Infrastructure\Notification\Notification\SubscriptionRenewalNotification;
use App\Models\User;
use Illuminate\Support\Facades\Notification;

beforeEach(function () {
    Notification::fake();
});

it('notifies users whose subscription renews in less than 5 days', function () {
    $user = User::factory()->create();
    $subscription = Subscription::factory()->for($user)->create([
        'state' => Subscription::ACTIVE,
        'next_payment' => now()->addDays(3),
        'notified_at' => null,
    ]);

    $this->artisan('app:notify-subscription-renewal')->assertSuccessful();

    Notification::assertSentTo($user, SubscriptionRenewalNotification::class);
    expect($subscription->fresh()->notified_at)->not->toBeNull();
});

it('does not notify when next_payment is more than 5 days away', function () {
    $user = User::factory()->create();
    Subscription::factory()->for($user)->create([
        'state' => Subscription::ACTIVE,
        'next_payment' => now()->addDays(10),
        'notified_at' => null,
    ]);

    $this->artisan('app:notify-subscription-renewal')->assertSuccessful();

    Notification::assertNothingSent();
});

it('does not notify inactive subscriptions', function () {
    $user = User::factory()->create();
    Subscription::factory()->inactive()->for($user)->create([
        'next_payment' => now()->addDays(5),
        'notified_at' => null,
    ]);

    $this->artisan('app:notify-subscription-renewal')->assertSuccessful();

    Notification::assertNothingSent();
});

it('does not notify twice for the same subscription', function () {
    $user = User::factory()->create();
    Subscription::factory()->for($user)->create([
        'state' => Subscription::ACTIVE,
        'next_payment' => now()->addDays(5),
        'notified_at' => now()->subDay(),
    ]);

    $this->artisan('app:notify-subscription-renewal')->assertSuccessful();

    Notification::assertNothingSent();
});

it('skips subscriptions whose user has been deleted', function () {
    Subscription::factory()->create([
        'user_id' => null,
        'state' => Subscription::ACTIVE,
        'next_payment' => now()->addDays(5),
        'notified_at' => null,
    ]);

    $this->artisan('app:notify-subscription-renewal')->assertSuccessful();

    Notification::assertNothingSent();
});

it('builds a mail message with renewal date and link to users.edit', function () {
    $subscription = Subscription::factory()->create([
        'next_payment' => now()->addDays(5)->setDate(2026, 5, 2),
    ]);

    $mail = (new SubscriptionRenewalNotification($subscription))->toMail();

    expect($mail->subject)->toContain('Renouvellement')
        ->and($mail->actionUrl)->toBe(route('users.edit'))
        ->and(implode(' ', $mail->introLines))->toContain('02/05/2026');
});
