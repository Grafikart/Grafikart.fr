<?php

namespace App\Infrastructure\Payment\Paypal;

use App\Domain\Premium\Entity\Plan;
use PayPal\Rest\ApiContext;
use Symfony\Component\HttpClient\HttpClient;

class PaypalApi
{

    private ApiContext $context;

    public function __construct(ApiContext $context)
    {
        $this->context = $context;
    }

    public function syncPlan(Plan $plan): void
    {
        if ($plan->getPaypalId() !== null) {
            return;
        }
        $data = [
            'product_id'          => 'PROD-9E352836F5615324R',
            'name'                => $plan->getName(),
            "description"         => $plan->getName(),
            "billing_cycles"      => [
                [
                    "pricing_scheme" => [
                        "fixed_price" => [
                            "value"         => $plan->getPrice(),
                            "currency_code" => "EUR"
                        ]
                    ],
                    "frequency"      => [
                        "interval_unit"  => "MONTH",
                        "interval_count" => $plan->getDuration()
                    ],
                    "tenure_type"    => "REGULAR",
                    "sequence"       => 1,
                    "total_cycles"   => 0
                ]
            ],
            "taxes"               => [
                "percentage" => $plan->getTax(),
                "inclusive"  => false,
            ],
            "payment_preferences" => [
                'auto_bill_outstanding'     => true,
                'payment_failure_threshold' => 0
            ]
        ];
        $client = HttpClient::create();

        // Obtention de l'Oauth
        $token = $this->context->getCredential()->getAccessToken([
            'grant_type' => 'client_credentials'
        ]);

        // Création du plan paypal
        $response = $client->request('POST', 'https://api.sandbox.paypal.com/v1/billing/plans', [
            'json'    => $data,
            'headers' => [
                'Accept'        => 'application/json',
                'Authorization' => 'Bearer ' . $token
            ]
        ]);
        $data = json_decode($response->getContent(false), true);
        if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
            $plan->setPaypalId($data['id']);
        } else {
            dd($data['message'], $data['details']);
        }
    }

    public function approvePlan(Plan $plan): string
    {

        $data = [
            'plan_id'          => $plan->getPaypalId(),
            'application_context' => [
                'brand_name' => 'Grafikart.fr',
                'shipping_preference' => 'NO_SHIPPING',
                'user_action' => 'CONTINUE',
                'return_url' => 'https://www.grafikart.fr/'
            ]
        ];


        $token = $this->context->getCredential()->getAccessToken([
            'grant_type' => 'client_credentials'
        ]);

        // Création du plan paypal
        $client = HttpClient::create();
        $response = $client->request('POST', 'https://api.sandbox.paypal.com/v1/billing/subscriptions', [
            'json'    => $data,
            'headers' => [
                'Accept'        => 'application/json',
                'Authorization' => 'Bearer ' . $token
            ]
        ]);
        $data = json_decode($response->getContent(false), true);
        if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
            dd($data);
        } else {
            dd($data['message'], $data['details']);
        }
    }
}
