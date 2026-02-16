<?php

namespace App\Infrastructure\Payment\Stripe\Factory;

use App\Domains\Premium\Models\Plan;
use App\Models\User;
use DateTimeInterface;

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

    /**
     * @return array<string, mixed>
     */
    public function subscriptionCreated(User $user, Plan $plan, DateTimeInterface $nextPaymentDate): array
    {
        $priceInCents = $plan->price * 100;
        $faker = fake();
        $subId = 'sub_'.fake()->unique()->regexify('[A-Za-z0-9]{24}');
        $siId = 'si_'.fake()->unique()->regexify('[A-Za-z0-9]{14}');
        $pmId = 'pm_'.fake()->unique()->regexify('[A-Za-z0-9]{24}');
        $evtId = 'evt_'.fake()->unique()->regexify('[A-Za-z0-9]{24}');
        $inId = 'in_'.fake()->unique()->regexify('[A-Za-z0-9]{24}');
        $txrId = 'txr_'.fake()->unique()->regexify('[A-Za-z0-9]{24}');
        $now = time();
        $nextPaymentTimestamp = $nextPaymentDate->getTimestamp();

        return [
            'id' => $evtId,
            'object' => 'event',
            'api_version' => '2026-01-28.clover',
            'created' => $now,
            'data' => [
                'object' => [
                    'id' => $subId,
                    'object' => 'subscription',
                    'application' => null,
                    'application_fee_percent' => null,
                    'automatic_tax' => [
                        'disabled_reason' => null,
                        'enabled' => false,
                        'liability' => null,
                    ],
                    'billing_cycle_anchor' => $now,
                    'billing_cycle_anchor_config' => null,
                    'billing_mode' => [
                        'flexible' => [
                            'proration_discounts' => 'included',
                        ],
                        'type' => 'flexible',
                        'updated_at' => $now - 16,
                    ],
                    'billing_thresholds' => null,
                    'cancel_at' => null,
                    'cancel_at_period_end' => false,
                    'canceled_at' => null,
                    'cancellation_details' => [
                        'comment' => null,
                        'feedback' => null,
                        'reason' => null,
                    ],
                    'collection_method' => 'charge_automatically',
                    'created' => $now,
                    'currency' => 'eur',
                    'customer' => $user->stripe_id,
                    'customer_account' => null,
                    'days_until_due' => null,
                    'default_payment_method' => $pmId,
                    'default_source' => null,
                    'default_tax_rates' => [],
                    'description' => null,
                    'discounts' => [],
                    'ended_at' => null,
                    'invoice_settings' => [
                        'account_tax_ids' => null,
                        'issuer' => [
                            'type' => 'self',
                        ],
                    ],
                    'items' => [
                        'object' => 'list',
                        'data' => [
                            [
                                'id' => $siId,
                                'object' => 'subscription_item',
                                'billing_thresholds' => null,
                                'created' => $now + 1,
                                'current_period_end' => $nextPaymentTimestamp,
                                'current_period_start' => $now,
                                'discounts' => [],
                                'metadata' => [],
                                'plan' => [
                                    'id' => $plan->stripe_id,
                                    'object' => 'plan',
                                    'active' => true,
                                    'amount' => $priceInCents,
                                    'amount_decimal' => (string) $priceInCents,
                                    'billing_scheme' => 'per_unit',
                                    'created' => $now - 427104,
                                    'currency' => 'eur',
                                    'interval' => 'month',
                                    'interval_count' => $plan->duration,
                                    'livemode' => false,
                                    'metadata' => [],
                                    'meter' => null,
                                    'nickname' => null,
                                    'product' => 'prod_'.fake()->regexify('[A-Za-z0-9]{14}'),
                                    'tiers_mode' => null,
                                    'transform_usage' => null,
                                    'trial_period_days' => null,
                                    'usage_type' => 'licensed',
                                ],
                                'price' => [
                                    'id' => $plan->stripe_id,
                                    'object' => 'price',
                                    'active' => true,
                                    'billing_scheme' => 'per_unit',
                                    'created' => $now - 427104,
                                    'currency' => 'eur',
                                    'custom_unit_amount' => null,
                                    'livemode' => false,
                                    'lookup_key' => null,
                                    'metadata' => [],
                                    'nickname' => null,
                                    'product' => 'prod_'.fake()->regexify('[A-Za-z0-9]{14}'),
                                    'recurring' => [
                                        'interval' => 'month',
                                        'interval_count' => $plan->duration,
                                        'meter' => null,
                                        'trial_period_days' => null,
                                        'usage_type' => 'licensed',
                                    ],
                                    'tax_behavior' => 'unspecified',
                                    'tiers_mode' => null,
                                    'transform_quantity' => null,
                                    'type' => 'recurring',
                                    'unit_amount' => $priceInCents,
                                    'unit_amount_decimal' => (string) $priceInCents,
                                ],
                                'quantity' => 1,
                                'subscription' => $subId,
                                'tax_rates' => [
                                    [
                                        'id' => $txrId,
                                        'object' => 'tax_rate',
                                        'active' => true,
                                        'country' => 'FR',
                                        'created' => $now - 449508,
                                        'description' => 'TVA FR',
                                        'display_name' => 'TVA',
                                        'effective_percentage' => 20.0,
                                        'flat_amount' => null,
                                        'inclusive' => true,
                                        'jurisdiction' => null,
                                        'jurisdiction_level' => null,
                                        'livemode' => false,
                                        'metadata' => [],
                                        'percentage' => 20.0,
                                        'rate_type' => 'percentage',
                                        'state' => null,
                                        'tax_type' => null,
                                    ],
                                ],
                            ],
                        ],
                        'has_more' => false,
                        'total_count' => 1,
                        'url' => "/v1/subscription_items?subscription={$subId}",
                    ],
                    'latest_invoice' => $inId,
                    'livemode' => false,
                    'metadata' => [
                        'plan_id' => (string) $plan->id,
                    ],
                    'next_pending_invoice_item_invoice' => null,
                    'on_behalf_of' => null,
                    'pause_collection' => null,
                    'payment_settings' => [
                        'payment_method_options' => [
                            'acss_debit' => null,
                            'bancontact' => null,
                            'card' => [
                                'network' => null,
                                'request_three_d_secure' => 'automatic',
                            ],
                            'customer_balance' => null,
                            'konbini' => null,
                            'payto' => null,
                            'sepa_debit' => null,
                            'us_bank_account' => null,
                        ],
                        'payment_method_types' => ['card'],
                        'save_default_payment_method' => 'off',
                    ],
                    'pending_invoice_item_interval' => null,
                    'pending_setup_intent' => null,
                    'pending_update' => null,
                    'plan' => [
                        'id' => $plan->stripe_id,
                        'object' => 'plan',
                        'active' => true,
                        'amount' => $priceInCents,
                        'amount_decimal' => (string) $priceInCents,
                        'billing_scheme' => 'per_unit',
                        'created' => $now - 427104,
                        'currency' => 'eur',
                        'interval' => 'month',
                        'interval_count' => $plan->duration,
                        'livemode' => false,
                        'metadata' => [],
                        'meter' => null,
                        'nickname' => null,
                        'product' => 'prod_'.fake()->regexify('[A-Za-z0-9]{14}'),
                        'tiers_mode' => null,
                        'transform_usage' => null,
                        'trial_period_days' => null,
                        'usage_type' => 'licensed',
                    ],
                    'quantity' => 1,
                    'schedule' => null,
                    'start_date' => $now,
                    'status' => 'active',
                    'test_clock' => null,
                    'transfer_data' => null,
                    'trial_end' => null,
                    'trial_settings' => [
                        'end_behavior' => [
                            'missing_payment_method' => 'create_invoice',
                        ],
                    ],
                    'trial_start' => null,
                ],
            ],
            'livemode' => false,
            'pending_webhooks' => 2,
            'request' => [
                'id' => null,
                'idempotency_key' => $faker->uuid(),
            ],
            'type' => 'customer.subscription.created',
        ];
    }
}
