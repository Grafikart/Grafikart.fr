<?php

use App\Domains\Premium\Models\Plan;
use App\Domains\Premium\Models\Subscription;
use App\Domains\Premium\Models\Transaction;
use App\Infrastructure\Payment\Payment;
use App\Infrastructure\Payment\Stripe\Factory\StripeAPIDataFactory;
use App\Infrastructure\Payment\Stripe\Factory\StripeEventFactory;
use App\Infrastructure\Payment\Stripe\StripeApi;
use App\Infrastructure\Payment\Stripe\StripePaymentFactory;
use App\Models\User;
use Illuminate\Testing\TestResponse;
use Stripe\Checkout\Session;
use Stripe\Invoice;
use Tests\TestCase;

function sendWebhookEvent(TestCase $test, array $eventData): TestResponse
{
    $secret = 'whsec_test_secret';
    config(['services.stripe.webhook_secret' => $secret]);

    $payload = json_encode($eventData);
    $timestamp = time();
    $signature = hash_hmac('sha256', "{$timestamp}.{$payload}", $secret);

    return $test->call('POST', '/api/stripe/webhook', [], [], [], [
        'HTTP_STRIPE_SIGNATURE' => "t={$timestamp},v1={$signature}",
        'CONTENT_TYPE' => 'application/json',
    ], $payload);
}

describe('payment_intent.succeeded', function () {
    it('creates a transaction and extends user premium', function () {
        $user = User::factory()->create(['stripe_id' => 'cus_test123']);
        $plan = Plan::factory()->create(['price' => 5, 'duration' => 1]);

        $payment = new Payment(
            id: 'pi_test123',
            amount: 500,
            planId: $plan->id,
            vat: 100,
            fee: 25,
            method: 'stripe',
            firstname: 'John',
            lastname: 'Doe',
            address: '123 Street',
            postalCode: '75001',
            countryCode: 'FR',
        );

        $this->mock(StripePaymentFactory::class)
            ->shouldReceive('createPaymentFromIntent')
            ->once()
            ->andReturn($payment);

        sendWebhookEvent($this, [
            'id' => 'evt_test123',
            'object' => 'event',
            'type' => 'payment_intent.succeeded',
            'data' => [
                'object' => [
                    'object' => 'payment_intent',
                    'id' => 'pi_test123',
                    'customer' => 'cus_test123',
                ],
            ],
        ])->assertOk();

        $user->refresh();
        expect($user->premium_end_at)->not->toBeNull();
        expect($user->premium_end_at->isFuture())->toBeTrue();

        $transaction = Transaction::first();
        expect($transaction)->not->toBeNull();
        expect($transaction->user_id)->toBe($user->id);
        expect($transaction->price)->toBe(500);
        expect($transaction->duration)->toBe($plan->duration);
        expect($transaction->method)->toBe('stripe');
        expect($transaction->method_id)->toBe('pi_test123');
    });

    it('extends premium from current end date if already premium', function () {
        $user = User::factory()->create([
            'stripe_id' => 'cus_test123',
            'premium_end_at' => now()->addMonths(2),
        ]);
        $plan = Plan::factory()->create(['price' => 5, 'duration' => 1]);
        $expectedEnd = now()->addMonths(3);

        $payment = new Payment(
            id: 'pi_test456',
            amount: 500,
            planId: $plan->id,
            method: 'stripe',
        );

        $this->mock(StripePaymentFactory::class)
            ->shouldReceive('createPaymentFromIntent')
            ->once()
            ->andReturn($payment);

        sendWebhookEvent($this, [
            'id' => 'evt_test456',
            'object' => 'event',
            'type' => 'payment_intent.succeeded',
            'data' => [
                'object' => [
                    'object' => 'payment_intent',
                    'id' => 'pi_test456',
                    'customer' => 'cus_test123',
                ],
            ],
        ])->assertOk();

        $user->refresh();
        expect($user->premium_end_at->format('Y-m-d'))->toBe($expectedEnd->format('Y-m-d'));
    });

    it('throws when no plan matches payment amount', function () {
        User::factory()->create(['stripe_id' => 'cus_test123']);
        Plan::factory()->create(['price' => 5, 'duration' => 1]);

        $payment = new Payment(
            id: 'pi_test789',
            amount: 99999,
        );

        $this->mock(StripePaymentFactory::class)
            ->shouldReceive('createPaymentFromIntent')
            ->once()
            ->andReturn($payment);

        sendWebhookEvent($this, [
            'id' => 'evt_test789',
            'object' => 'event',
            'type' => 'payment_intent.succeeded',
            'data' => [
                'object' => [
                    'object' => 'payment_intent',
                    'id' => 'pi_test789',
                    'customer' => 'cus_test123',
                ],
            ],
        ])->assertServerError();

        expect(Transaction::count())->toBe(0);
    });

    it('persists automatic tax from the payment source', function (string $source, int $tax, string $feeSource = 'expanded') {
        $eventFactory = new StripeEventFactory;
        $apiFactory = new StripeAPIDataFactory;
        $user = User::factory()->create(['stripe_id' => 'cus_test123']);
        $plan = Plan::factory()->create(['price' => 5, 'duration' => 1]);
        $intent = $apiFactory->paymentIntent($user, $plan);
        $intent->amount_details = ['tip' => []];
        if ($source === 'invoice') {
            $intent->metadata = [];
        }
        $api = $this->mock(StripeApi::class);
        $expectedFee = $intent->latest_charge->balance_transaction->fee;
        if ($feeSource === 'id') {
            $balanceTransaction = $intent->latest_charge->balance_transaction;
            $intent->latest_charge->balance_transaction = $balanceTransaction->id;
            $api->shouldReceive('getTransaction')->once()->with($balanceTransaction->id)->andReturn($balanceTransaction);
        }
        $api->shouldReceive('getPaymentIntent')->once()->andReturn($intent);
        $api->shouldReceive('getInvoiceFromIntent')->once()->with($intent->id)->andReturn(
            $source === 'invoice' ? Invoice::constructFrom([
                'id' => 'in_test123',
                'total_taxes' => [['amount' => $tax, 'tax_behavior' => 'inclusive']],
                'parent' => ['subscription_details' => ['metadata' => ['plan_id' => (string) $plan->id]]],
            ]) : null
        );
        if ($source === 'checkout') {
            $api->shouldReceive('getCheckoutSessionFromIntent')->once()->with($intent->id)->andReturn(
                Session::constructFrom([
                    'id' => 'cs_test123',
                    'total_details' => ['amount_tax' => $tax],
                    'metadata' => ['plan_id' => (string) $plan->id],
                ])
            );
        }

        $response = sendWebhookEvent($this, $eventFactory->paymentIntentSucceeded($user, $plan));
        $response->assertOk();
        $transaction = Transaction::first();
        expect($transaction)->not->toBeNull();
        expect($transaction->user_id)->toBe($user->id);
        expect($transaction->price)->toBe(500);
        expect($transaction->duration)->toBe($plan->duration);
        expect($transaction->method)->toBe('stripe');
        expect($transaction->tax)->toBe($tax);
        expect($transaction->fee)->toBe($expectedFee);
    })->with([
        'one-time inclusive tax' => ['checkout', 83],
        'subscription inclusive tax without intent metadata' => ['invoice', 83],
        'one-time zero tax' => ['checkout', 0],
        'subscription zero tax' => ['invoice', 0],
        'fee retrieved by transaction id' => ['checkout', 83, 'id'],
    ]);

    it('rejects the payment when the balance transaction is not yet available', function () {
        $eventFactory = new StripeEventFactory;
        $apiFactory = new StripeAPIDataFactory;
        $user = User::factory()->create(['stripe_id' => 'cus_test123']);
        $plan = Plan::factory()->create(['price' => 5, 'duration' => 1]);
        $intent = $apiFactory->paymentIntent($user, $plan);
        $intent->latest_charge->balance_transaction = null;

        $this->mock(StripeApi::class)
            ->shouldReceive('getPaymentIntent')
            ->once()
            ->with($intent->id)
            ->andReturn($intent);

        $event = $eventFactory->paymentIntentSucceeded($user, $plan);
        $event['data']['object']['id'] = $intent->id;

        sendWebhookEvent($this, $event)->assertServerError();

        expect(Transaction::count())->toBe(0);
        expect($user->refresh()->premium_end_at)->toBeNull();
    });
});

describe('customer.subscription.created', function () {
    it('creates a subscription when the plan exists', function () {
        config([
            'services.stripe.public' => 'pk_stripe_demo',
            'services.stripe.secret' => 'sk_stripe_demo',
        ]);
        $eventFactory = new StripeEventFactory;
        $user = User::factory()->create(['stripe_id' => 'cus_test123']);
        $plan = Plan::factory()->create();
        $nextPayment = now()->endOfDay()->addMonth();

        $event = $eventFactory->subscriptionCreated($user, $plan, $nextPayment);
        $response = sendWebhookEvent($this, $event);
        $response->assertOk();

        $subscription = Subscription::first();
        expect($subscription)->not->toBeNull();
        expect($subscription->user_id)->toBe($user->id);
        expect($subscription->plan_id)->toBe($plan->id);
        expect($subscription->state)->toBe(Subscription::ACTIVE);
        expect($subscription->stripe_id)->toBe($event['data']['object']['id']);
        expect($subscription->next_payment->format('Y-m-d'))->toBe($nextPayment->format('Y-m-d'));
    });
});
