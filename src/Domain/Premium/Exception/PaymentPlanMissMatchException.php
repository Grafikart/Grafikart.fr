<?php

namespace App\Domain\Premium\Exception;

use Throwable;

class PaymentPlanMissMatchException extends \Exception
{
    public function __construct(string $message = "La somme du paiement ne correspond à aucun type d'abonnement", int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
