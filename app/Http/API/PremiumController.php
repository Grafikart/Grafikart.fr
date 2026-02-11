<?php

namespace App\Http\API;

use App\Domains\Premium\Models\Plan;
use App\Infrastructure\Payment\Event\PaymentEvent;
use App\Infrastructure\Payment\Paypal\PaypalService;
use App\Infrastructure\Payment\Stripe\StripeApi;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Capture the payment request from providers and generate a payment
 */
class PremiumController
{
    public function stripe(Plan $plan, StripeApi $api, \Illuminate\Http\Request $request)
    {
        $isSubscription = $request->boolean('subscription');
        $url = route('premium', absolute: true);
        $user = $request->user();
        assert($user instanceof User);
        try {
            $api->createCustomer($user);
            $session = $isSubscription ? $api->createSuscriptionSession($user, $plan, $url) : $api->createPaymentSession($user, $plan, $url);

            return response()->json([
                'url' => $session->url,
                'secret' => $session->client_secret,
                'id' => $session->id,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Impossible de contacter l\'API Stripe',
            ], 422);
        }
    }

    public function paypal(string $orderId, PaypalService $paypal, Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            assert($user instanceof User);
            $payment = $paypal->capture($orderId);
            event(new PaymentEvent($payment, $user));

            return response()->json([]);
        } catch (\Error $e) {
            return response()->json([
                'title' => 'Erreur lors du paiement',
                'detail' => $e->getMessage(),
            ], 422);
        }
    }
}
