<?php

namespace App\Infrastructure\Payment;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Ajoute des méthodes permettant de récupérer des infos publics pour les paiements.
 */
class PaymentTwigExtension extends AbstractExtension
{
    public function __construct(private readonly string $stripePublicKey = '', private readonly string $paypalClientId = '')
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('stripeKey', [$this, 'getStripePublicKey']),
            new TwigFunction('paypalClientId', [$this, 'getPaypalClientId']),
        ];
    }

    public function getStripePublicKey(): string
    {
        return $this->stripePublicKey;
    }

    public function getPaypalClientId(): string
    {
        return $this->paypalClientId;
    }
}
