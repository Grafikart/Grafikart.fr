<?php

namespace App\Infrastructure\Payment\Exception;

use PayPalHttp\HttpException;

class PaymentFailedException extends \Exception
{
    public static function fromPaypalHttpException(HttpException $exception): self
    {
        $data = json_decode($exception->getMessage(), true, 512, JSON_THROW_ON_ERROR);

        return new self($data['details'][0]['description'] ?? $exception->getMessage());
    }
}
