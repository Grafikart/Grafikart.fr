<?php

namespace App\Http\Front\Account;

use App\Infrastructure\Payment\Stripe\StripeApi;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

readonly class SubscriptionController
{
    public function __construct(private StripeApi $api) {}

    public function manage(Request $request): RedirectResponse
    {
        $user = $request->user();
        assert($user instanceof User);
        $redirectUrl = route('transactions.index');

        if ($user->stripe_id === null) {
            return redirect($redirectUrl)->with('error', "Vous n'avez pas d'abonnement actif");
        }

        return redirect($this->api->getBillingUrl($user, $redirectUrl));
    }
}
