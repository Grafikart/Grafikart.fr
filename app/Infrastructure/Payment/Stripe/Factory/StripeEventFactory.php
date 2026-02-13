<?php

namespace App\Infrastructure\Payment\Stripe\Factory;

use App\Domains\Premium\Models\Plan;
use App\Models\User;

class StripeEventFactory
{
    /**
     * @return array<string, mixed>
     */
    public function paymentIntentSucceeded(User $user, Plan $plan): array
    {
        $priceInCents = $plan->price * 100;
        $faker = fake();
        $piId = 'pi_'.fake()->unique()->regexify('[A-Za-z0-9]{24}');
        $chId = 'ch_'.fake()->unique()->regexify('[A-Za-z0-9]{24}');
        $pmId = 'pm_'.fake()->unique()->regexify('[A-Za-z0-9]{24}');
        $evtId = 'evt_'.fake()->unique()->regexify('[A-Za-z0-9]{24}');

        return [
            'id' => $evtId,
            'object' => 'event',
            'api_version' => '2026-01-28.clover',
            'created' => time(),
            'data' => [
                'object' => [
                    'id' => $piId,
                    'object' => 'payment_intent',
                    'amount' => $priceInCents,
                    'amount_capturable' => 0,
                    'amount_details' => [
                        'tip' => [],
                    ],
                    'amount_received' => $priceInCents,
                    'application' => null,
                    'application_fee_amount' => null,
                    'automatic_payment_methods' => null,
                    'canceled_at' => null,
                    'cancellation_reason' => null,
                    'capture_method' => 'automatic',
                    'client_secret' => "{$piId}_secret_".fake()->regexify('[A-Za-z0-9]{24}'),
                    'confirmation_method' => 'automatic',
                    'created' => time() - 1,
                    'currency' => 'eur',
                    'customer' => $user->stripe_id,
                    'customer_account' => null,
                    'description' => 'Subscription creation',
                    'excluded_payment_method_types' => null,
                    'last_payment_error' => null,
                    'latest_charge' => $chId,
                    'livemode' => false,
                    'metadata' => [],
                    'next_action' => null,
                    'on_behalf_of' => null,
                    'payment_details' => [
                        'customer_reference' => null,
                        'order_reference' => 'cs_test_'.fake()->regexify('[A-Za-z0-9]{58}'),
                    ],
                    'payment_method' => $pmId,
                    'payment_method_configuration_details' => null,
                    'payment_method_options' => [
                        'card' => [
                            'installments' => null,
                            'mandate_options' => null,
                            'network' => null,
                            'request_three_d_secure' => 'automatic',
                            'setup_future_usage' => 'off_session',
                        ],
                    ],
                    'payment_method_types' => ['card'],
                    'processing' => null,
                    'receipt_email' => null,
                    'review' => null,
                    'setup_future_usage' => 'off_session',
                    'shipping' => null,
                    'source' => null,
                    'statement_descriptor' => null,
                    'statement_descriptor_suffix' => null,
                    'status' => 'succeeded',
                    'transfer_data' => null,
                    'transfer_group' => null,
                ],
            ],
            'livemode' => false,
            'pending_webhooks' => 2,
            'request' => [
                'id' => null,
                'idempotency_key' => $faker->uuid(),
            ],
            'type' => 'payment_intent.succeeded',
        ];
    }
}
