<?php

declare(strict_types=1);

use App\Infrastructure\Payment\PaymentTwigExtension;
use App\Infrastructure\Payment\Stripe\StripeApi;
use App\Infrastructure\Payment\Stripe\StripeEventValueResolver;
use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\ProductionEnvironment;
use PayPalCheckoutSdk\Core\SandboxEnvironment;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();

    $parameters->set('stripe_public_key', '%env(resolve:STRIPE_ID)%');

    $parameters->set('stripe_secret_key', '%env(resolve:STRIPE_SECRET)%');

    $parameters->set('stripe_webhook_secret', '%env(resolve:STRIPE_WEBHOOK_SECRET)%');

    $parameters->set('paypal_id', '%env(resolve:PAYPAL_ID)%');

    $parameters->set('paypal_secret', '%env(resolve:PAYPAL_SECRET)%');

    $services = $containerConfigurator->services();

    $services->defaults()
        ->autowire()
        ->autoconfigure();

    $services->set(PaymentTwigExtension::class)
        ->args([
        '%stripe_public_key%',
        '%paypal_id%',
    ]);

    $services->set(StripeApi::class)
        ->args([
        '%stripe_secret_key%',
    ]);

    $services->set(StripeEventValueResolver::class)
        ->args([
        '%stripe_webhook_secret%',
    ])
        ->tag('controller.argument_value_resolver', [
        'priority' => 50,
    ]);

    $services->set('paypal_sandbox_environment', SandboxEnvironment::class)
        ->args([
        '%paypal_id%',
        '%paypal_secret%',
    ]);

    $services->set('paypal_production_environment', ProductionEnvironment::class)
        ->args([
        '%paypal_id%',
        '%paypal_secret%',
    ]);

    $services->set(PayPalHttpClient::class)
        ->args([
        service('paypal_sandbox_environment'),
    ]);
};
