<?php

use App\Domains\Premium\Models\Plan;
use App\Infrastructure\Payment\Stripe\StripeApi;
use App\Models\User;
use Stripe\ApiRequestor;
use Stripe\HttpClient\ClientInterface;
use Stripe\Invoice;

it('sends requests with the pinned Stripe API version', function () {
    $previousClient = ApiRequestor::httpClient();
    $httpClient = Mockery::mock(ClientInterface::class);
    $httpClient->shouldReceive('request')->once()->withArgs(
        fn ($method, $url, $headers) => $method === 'get'
            && str_ends_with($url, '/v1/customers/cus_test123')
            && in_array('Stripe-Version: 2026-01-28.clover', $headers, true)
    )->andReturn(['{"id":"cus_test123","object":"customer"}', 200, []]);

    ApiRequestor::setHttpClient($httpClient);
    try {
        $customer = (new StripeApi('sk_test_example'))->getCustomer('cus_test123');
        expect($customer->id)->toBe('cus_test123');
    } finally {
        ApiRequestor::setHttpClient($previousClient);
        Mockery::close();
    }
});

it('calculates automatic taxes using the collected customer address', function (string $method, string $mode) {
    $previousClient = ApiRequestor::httpClient();
    $httpClient = Mockery::mock(ClientInterface::class);
    $httpClient->shouldReceive('request')->once()->withArgs(
        fn ($verb, $url, $headers, $params) => $verb === 'post'
            && str_ends_with($url, '/v1/checkout/sessions')
            && in_array('Stripe-Version: 2026-01-28.clover', $headers, true)
            && $params['mode'] === $mode
            && $params['automatic_tax']['enabled'] === 'true'
            && $params['billing_address_collection'] === 'required'
            && $params['customer_update']['address'] === 'auto'
            && $params['customer'] === 'cus_test123'
            && ! isset($params['line_items'][0]['dynamic_tax_rates'])
            && ($mode === 'payment'
                ? $params['line_items'][0]['price_data']['tax_behavior'] === 'inclusive'
                    && $params['line_items'][0]['price_data']['unit_amount'] == 500
                : $params['line_items'][0]['price'] === 'price_test123')
    )->andReturn(['{"id":"cs_test123","object":"checkout.session"}', 200, []]);

    ApiRequestor::setHttpClient($httpClient);
    try {
        $user = new User;
        $user->stripe_id = 'cus_test123';
        $plan = new Plan(['name' => 'Premium', 'price' => 5, 'stripe_id' => 'price_test123']);
        $session = (new StripeApi('sk_test_example'))->$method($user, $plan, 'https://example.com/premium');
        expect($session->id)->toBe('cs_test123');
    } finally {
        ApiRequestor::setHttpClient($previousClient);
        Mockery::close();
    }
})->with([
    ['createPaymentSession', 'payment'],
    ['createSuscriptionSession', 'subscription'],
]);

it('finds the expanded invoice by PaymentIntent', function (bool $hasInvoice) {
    $previousClient = ApiRequestor::httpClient();
    $httpClient = Mockery::mock(ClientInterface::class);
    $httpClient->shouldReceive('request')->once()->withArgs(
        fn ($verb, $url, $headers, $params) => $verb === 'get'
            && str_ends_with($url, '/v1/invoice_payments')
            && $params['payment'] === ['type' => 'payment_intent', 'payment_intent' => 'pi_test123']
            && $params['expand'] === ['data.invoice']
    )->andReturn([json_encode([
        'object' => 'list',
        'data' => $hasInvoice ? [[
            'object' => 'invoice_payment',
            'invoice' => [
                'id' => 'in_test123',
                'object' => 'invoice',
                'total_taxes' => [['amount' => 83]],
            ],
        ]] : [],
        'has_more' => false,
    ]), 200, []]);

    ApiRequestor::setHttpClient($httpClient);
    try {
        $invoice = (new StripeApi('sk_test_example'))->getInvoiceFromIntent('pi_test123');
        if ($hasInvoice) {
            expect($invoice)->toBeInstanceOf(Invoice::class);
            expect($invoice->total_taxes[0]->amount)->toBe(83);
        } else {
            expect($invoice)->toBeNull();
        }
    } finally {
        ApiRequestor::setHttpClient($previousClient);
        Mockery::close();
    }
})->with([true, false]);
