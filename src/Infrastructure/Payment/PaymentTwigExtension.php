<?php

namespace App\Infrastructure\Payment;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Ajoute des méthodes permettant de récupérer des infos publics pour les paiements.
 */
class PaymentTwigExtension extends AbstractExtension
{
    private string $stripePublicKey;
    private string $paypalClientId;

    public function __construct(string $stripePublicKey = '', string $paypalClientId = '')
    {
        $this->stripePublicKey = $stripePublicKey;
        $this->paypalClientId = $paypalClientId;
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
