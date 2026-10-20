<?php

use App\Domains\Premium\Models\Plan;
use App\Infrastructure\Payment\Stripe\StripeApi;
use App\Models\User;
use Stripe\ApiRequestor;
use Stripe\HttpClient\ClientInterface;

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

it('preserves dynamic taxes in Checkout requests on Clover', function (string $method, string $mode) {
    $previousClient = ApiRequestor::httpClient();
    $httpClient = Mockery::mock(ClientInterface::class);
    $httpClient->shouldReceive('request')->once()->withArgs(
        fn ($verb, $url, $headers, $params) => $verb === 'post'
            && str_ends_with($url, '/v1/checkout/sessions')
            && in_array('Stripe-Version: 2026-01-28.clover', $headers, true)
            && $params['mode'] === $mode
            && $params['line_items'][0]['dynamic_tax_rates'] === ['txr_1SzcN4FLYwyu3L7pEz2Rnhlc']
    )->andReturn(['{"id":"cs_test123","object":"checkout.session"}', 200, []]);

    ApiRequestor::setHttpClient($httpClient);
    try {
        $user = new User(['stripe_id' => 'cus_test123']);
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
