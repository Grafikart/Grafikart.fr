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
    public function __construct(private readonly string $webhookSecret)
    {
    }

    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        return Event::class === $argument->getType();
    }

    public function resolve(Request $request, ArgumentMetadata $argument): \Generator
    {
        yield Webhook::constructEvent(
            $request->getContent(false),
            (string) $request->headers->get('stripe-signature'),
            $this->webhookSecret
        );
    }
}
