<?php

namespace App\Infrastructure\Payment\Stripe;

use Stripe\Event;
use Stripe\Webhook;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

/**
 * Injecte un évènement stripe dans les action d'un controller en validant la signature.
 */
class StripeEventValueResolver implements ArgumentValueResolverInterface
{
    private string $webhookSecret;

    public function __construct(string $webhookSecret)
    {
        $this->webhookSecret = $webhookSecret;
    }

    public function supports(Request $request, ArgumentMetadata $argument)
    {
        return Event::class === $argument->getType();
    }

    public function resolve(Request $request, ArgumentMetadata $argument)
    {
        yield Webhook::constructEvent(
            $request->getContent(false),
            (string) $request->headers->get('stripe-signature'),
            $this->webhookSecret
        );
    }
}
