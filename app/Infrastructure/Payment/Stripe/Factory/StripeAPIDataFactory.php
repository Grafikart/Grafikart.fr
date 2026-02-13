<?php

namespace App\Infrastructure\Payment\Stripe\Factory;

use App\Domains\Premium\Models\Plan;
use App\Models\User;
use Stripe\PaymentIntent;

/**
 * Create fake stripe objects to test Stripe
 */
class StripeAPIDataFactory
{
    public function paymentIntent(User $user, Plan $plan): PaymentIntent
    {
        $priceInCents = $plan->price * 100;
        $faker = fake();
        $piId = 'pi_'.fake()->unique()->regexify('[A-Za-z0-9]{24}');
        $chId = 'ch_'.fake()->unique()->regexify('[A-Za-z0-9]{24}');
        $txnId = 'txn_'.fake()->unique()->regexify('[A-Za-z0-9]{24}');
        $pmId = 'pm_'.fake()->unique()->regexify('[A-Za-z0-9]{24}');
        $prodId = 'prod_'.fake()->unique()->regexify('[A-Za-z0-9]{14}');
        $feeAmount = (int) round($priceInCents * 0.015 + 25);
        $taxAmount = (int) round($priceInCents * 0.2);
        $unitCost = $priceInCents - $taxAmount;

        return PaymentIntent::constructFrom([
            'id' => $piId,
            'object' => 'payment_intent',
            'amount' => $priceInCents,
            'amount_capturable' => 0,
            'amount_details' => [
                'line_items' => [
                    'object' => 'list',
                    'data' => [
                        [
                            'id' => 'uli_'.fake()->regexify('[A-Za-z0-9]{14}'),
                            'object' => 'payment_intent_amount_details_line_item',
                            'discount_amount' => null,
                            'payment_method_options' => null,
                            'product_code' => $prodId,
                            'product_name' => $plan->name,
                            'quantity' => 1,
                            'tax' => null,
                            'unit_cost' => $unitCost,
                            'unit_of_measure' => null,
                        ],
                    ],
                    'has_more' => false,
                    'url' => "/v1/payment_intents/{$piId}/amount_details_line_items",
                ],
                'shipping' => [
                    'amount' => 0,
                    'from_postal_code' => null,
                    'to_postal_code' => null,
                ],
                'tax' => [
                    'total_tax_amount' => $taxAmount,
                ],
                'tip' => [],
            ],
            'amount_received' => $priceInCents,
            'application' => null,
            'application_fee_amount' => null,
            'automatic_payment_methods' => null,
            'canceled_at' => null,
            'cancellation_reason' => null,
            'capture_method' => 'automatic_async',
            'client_secret' => "{$piId}_secret_".fake()->regexify('[A-Za-z0-9]{24}'),
            'confirmation_method' => 'automatic',
            'created' => time(),
            'currency' => 'eur',
            'customer' => $user->stripe_id,
            'customer_account' => null,
            'description' => null,
            'excluded_payment_method_types' => null,
            'last_payment_error' => null,
            'latest_charge' => [
                'id' => $chId,
                'object' => 'charge',
                'amount' => $priceInCents,
                'amount_captured' => $priceInCents,
                'amount_refunded' => 0,
                'application' => null,
                'application_fee' => null,
                'application_fee_amount' => null,
                'balance_transaction' => [
                    'id' => $txnId,
                    'object' => 'balance_transaction',
                    'amount' => $priceInCents,
                    'available_on' => now()->addWeek()->startOfDay()->getTimestamp(),
                    'balance_type' => 'payments',
                    'created' => time(),
                    'currency' => 'eur',
                    'description' => null,
                    'exchange_rate' => null,
                    'fee' => $feeAmount,
                    'fee_details' => [
                        [
                            'amount' => $feeAmount,
                            'application' => null,
                            'currency' => 'eur',
                            'description' => 'Stripe processing fees',
                            'type' => 'stripe_fee',
                        ],
                    ],
                    'net' => $priceInCents - $feeAmount,
                    'reporting_category' => 'charge',
                    'source' => $chId,
                    'status' => 'pending',
                    'type' => 'charge',
                ],
                'billing_details' => [
                    'address' => [
                        'city' => $faker->city(),
                        'country' => $faker->countryCode(),
                        'line1' => $faker->streetAddress(),
                        'line2' => null,
                        'postal_code' => $faker->postcode(),
                        'state' => null,
                    ],
                    'email' => $faker->email(),
                    'name' => $faker->lastName(),
                    'phone' => null,
                    'tax_id' => null,
                ],
                'calculated_statement_descriptor' => 'GRAFIKART SANDBOX',
                'captured' => true,
                'created' => time(),
                'currency' => 'eur',
                'customer' => $user->stripe_id,
                'description' => null,
                'destination' => null,
                'dispute' => null,
                'disputed' => false,
                'failure_balance_transaction' => null,
                'failure_code' => null,
                'failure_message' => null,
                'fraud_details' => [],
                'livemode' => false,
                'metadata' => [
                    'plan_id' => (string) $plan->id,
                ],
                'on_behalf_of' => null,
                'order' => null,
                'outcome' => [
                    'advice_code' => null,
                    'network_advice_code' => null,
                    'network_decline_code' => null,
                    'network_status' => 'approved_by_network',
                    'reason' => null,
                    'risk_level' => 'normal',
                    'risk_score' => $faker->numberBetween(10, 70),
                    'seller_message' => 'Payment complete.',
                    'type' => 'authorized',
                ],
                'paid' => true,
                'payment_intent' => $piId,
                'payment_method' => $pmId,
                'payment_method_details' => [
                    'card' => [
                        'amount_authorized' => $priceInCents,
                        'authorization_code' => $faker->numerify('######'),
                        'brand' => 'visa',
                        'checks' => [
                            'address_line1_check' => 'pass',
                            'address_postal_code_check' => 'pass',
                            'cvc_check' => null,
                        ],
                        'country' => 'US',
                        'exp_month' => $faker->numberBetween(1, 12),
                        'exp_year' => $faker->numberBetween(2030, 2050),
                        'extended_authorization' => [
                            'status' => 'disabled',
                        ],
                        'fingerprint' => fake()->regexify('[A-Za-z0-9]{16}'),
                        'funding' => 'credit',
                        'incremental_authorization' => [
                            'status' => 'unavailable',
                        ],
                        'installments' => null,
                        'last4' => '4242',
                        'mandate' => null,
                        'multicapture' => [
                            'status' => 'unavailable',
                        ],
                        'network' => 'visa',
                        'network_token' => [
                            'used' => false,
                        ],
                        'network_transaction_id' => $faker->numerify('###############'),
                        'overcapture' => [
                            'maximum_amount_capturable' => $priceInCents,
                            'status' => 'unavailable',
                        ],
                        'regulated_status' => 'unregulated',
                        'three_d_secure' => null,
                        'wallet' => [
                            'dynamic_last4' => null,
                            'link' => [],
                            'type' => 'link',
                        ],
                    ],
                    'type' => 'card',
                ],
                'radar_options' => [],
                'receipt_email' => null,
                'receipt_number' => null,
                'receipt_url' => 'https://pay.stripe.com/receipts/payment/'.fake()->regexify('[A-Za-z0-9]{40}'),
                'refunded' => false,
                'review' => null,
                'shipping' => null,
                'source' => null,
                'source_transfer' => null,
                'statement_descriptor' => null,
                'statement_descriptor_suffix' => null,
                'status' => 'succeeded',
                'transfer_data' => null,
                'transfer_group' => null,
            ],
            'livemode' => false,
            'metadata' => [
                'plan_id' => (string) $plan->id,
            ],
            'next_action' => null,
            'on_behalf_of' => null,
            'payment_details' => [
                'customer_reference' => null,
                'order_reference' => $prodId,
            ],
            'payment_method' => $pmId,
            'payment_method_configuration_details' => null,
            'payment_method_options' => [
                'card' => [
                    'installments' => null,
                    'mandate_options' => null,
                    'network' => null,
                    'request_three_d_secure' => 'automatic',
                ],
            ],
            'payment_method_types' => ['card'],
            'processing' => null,
            'receipt_email' => null,
            'review' => null,
            'setup_future_usage' => null,
            'shipping' => null,
            'source' => null,
            'statement_descriptor' => null,
            'statement_descriptor_suffix' => null,
            'status' => 'succeeded',
            'transfer_data' => null,
            'transfer_group' => null,
        ]);
    }
}
