<?php

use App\Domains\Premium\Models\Plan;
use App\Domains\Premium\Models\Transaction;
use App\Infrastructure\Payment\Event\PaymentEvent;
use App\Infrastructure\Payment\Payment;
use App\Infrastructure\Payment\Paypal\PaypalService;
use App\Infrastructure\Payment\Stripe\StripeApi;
use App\Models\User;
use Illuminate\Support\Facades\Event;
use Stripe\Checkout\Session;

describe('stripe', function () {
    it('requires authentication', function () {
        $plan = Plan::factory()->create();

        $this->postJson("/api/premium/{$plan->id}/stripe")
            ->assertUnauthorized();
    });

    it('creates a payment session and returns the url', function () {
        $user = User::factory()->create();
        $plan = Plan::factory()->create();

        $session = new Session('cs_test_123');
        $session->url = 'https://checkout.stripe.com/session_123';
        $session->client_secret = 'cs_secret';

        $mock = $this->mock(StripeApi::class);
        $mock->shouldReceive('createCustomer')->once()->with(\Mockery::on(fn ($u) => $u->id === $user->id))->andReturn($user);
        $mock->shouldReceive('createPaymentSession')->once()->andReturn($session);

        $this->actingAs($user)
            ->postJson("/api/premium/{$plan->id}/stripe?subscription=0")
            ->assertOk()
            ->assertJson([
                'url' => 'https://checkout.stripe.com/session_123',
                'secret' => 'cs_secret',
            ]);
    });

    it('creates a subscription session when subscription=1', function () {
        $user = User::factory()->create();
        $plan = Plan::factory()->create();

        $session = new Session('cs_test_456');
        $session->url = 'https://checkout.stripe.com/session_456';
        $session->client_secret = 'cs_secret_456';

        $mock = $this->mock(StripeApi::class);
        $mock->shouldReceive('createCustomer')->once()->andReturn($user);
        $mock->shouldReceive('createSuscriptionSession')->once()->andReturn($session);

        $this->actingAs($user)
            ->postJson("/api/premium/{$plan->id}/stripe?subscription=1")
            ->assertOk()
            ->assertJson([
                'url' => 'https://checkout.stripe.com/session_456',
            ]);
    });

    it('returns 422 when stripe api fails', function () {
        $user = User::factory()->create();
        $plan = Plan::factory()->create();

        $mock = $this->mock(StripeApi::class);
        $mock->shouldReceive('createCustomer')->once()->andThrow(new \Exception('Stripe error'));

        $this->actingAs($user)
            ->postJson("/api/premium/{$plan->id}/stripe")
            ->assertStatus(422)
            ->assertJson(['message' => "Impossible de contacter l'API Stripe"]);
    });
});

describe('paypal', function () {
    it('requires authentication', function () {
        $this->postJson('/api/premium/paypal/ORDER_123')
            ->assertUnauthorized();
    });

    it('captures payment and dispatches event', function () {
        Event::fake([PaymentEvent::class]);

        $user = User::factory()->create();
        $plan = Plan::factory()->create(['price' => 5, 'duration' => 1]);

        $payment = new Payment(
            id: 'ORDER_123',
            amount: 500,
            planId: $plan->id,
            vat: 100,
            fee: 25,
            method: 'paypal',
            firstname: 'John',
            lastname: 'Doe',
        );

        $mock = Mockery::mock(PaypalService::class);
        $mock->shouldReceive('capture')
            ->once()
            ->with('ORDER_123')
            ->andReturn($payment);
        $this->app->instance(PaypalService::class, $mock);

        $this->actingAs($user)
            ->postJson('/api/premium/paypal/ORDER_123')
            ->assertOk();

        Event::assertDispatched(PaymentEvent::class, function (PaymentEvent $event) use ($user) {
            return $event->getPayment()->id === 'ORDER_123'
                && $event->getUser()->id === $user->id;
        });
    });

    it('creates a transaction and extends premium on successful paypal payment', function () {
        $user = User::factory()->create();
        $plan = Plan::factory()->create(['price' => 5, 'duration' => 1]);

        $payment = new Payment(
            id: 'ORDER_456',
            amount: 500,
            planId: $plan->id,
            vat: 100,
            fee: 25,
            method: 'paypal',
        );

        $mock = Mockery::mock(PaypalService::class);
        $mock->shouldReceive('capture')
            ->once()
            ->with('ORDER_456')
            ->andReturn($payment);
        $this->app->instance(PaypalService::class, $mock);

        $this->actingAs($user)
            ->postJson('/api/premium/paypal/ORDER_456')
            ->assertOk();

        $user->refresh();
        expect($user->premium_end_at)->not->toBeNull();
        expect($user->premium_end_at->isFuture())->toBeTrue();

        $transaction = Transaction::first();
        expect($transaction)->not->toBeNull();
        expect($transaction->user_id)->toBe($user->id);
        expect($transaction->price)->toBe(500);
        expect($transaction->method)->toBe('paypal');
        expect($transaction->method_id)->toBe('ORDER_456');
    });

    it('returns 422 when paypal capture fails', function () {
        $user = User::factory()->create();

        $mock = Mockery::mock(PaypalService::class);
        $mock->shouldReceive('capture')
            ->once()
            ->andThrow(new \Error('Capture failed'));
        $this->app->instance(PaypalService::class, $mock);

        $this->actingAs($user)
            ->postJson('/api/premium/paypal/ORDER_FAIL')
            ->assertStatus(422)
            ->assertJson([
                'title' => 'Erreur lors du paiement',
                'detail' => 'Capture failed',
            ]);
    });
});
