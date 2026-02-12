<?php

namespace App\Infrastructure\Payment\Rules;

use App\Infrastructure\Payment\Stripe\StripeApi;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Stripe\Exception\InvalidRequestException;

class StripePriceId implements ValidationRule
{
    public function __construct() {}

    /**
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $api = app(StripeApi::class);
        assert($api instanceof StripeApi);
        if (! is_string($value) || $value === '') {
            $fail('La formule ":input" n\'existe pas sur stripe.');

            return;
        }

        try {
            $api->getPlan($value);
        } catch (InvalidRequestException) {
            $fail('La formule ":input" n\'existe pas sur stripe.');
        }
    }
}
