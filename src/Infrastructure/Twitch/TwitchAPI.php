<?php

namespace App\Infrastructure\Twitch;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class TwitchAPI
{

    public function __construct(
        #[Autowire(env: 'TWITCH_ID')]
        private string $id,
        #[Autowire(env: 'TWITCH_SECRET')]
        private string $secret,
        private HttpClientInterface $client,
    ) {
    }

    public function getSubscriptions(): array
    {
        $response = $this->client->request('GET', 'https://api.twitch.tv/helix/eventsub/subscriptions', [
            'headers' => [
                'Authorization' => "Bearer {$this->getAccessToken()}",
                'Client-ID' => $this->id,
            ]
        ]);
        return $response->toArray()['data'];
    }

    public function addWebhookSubscription(): void
    {
        $types = ['stream.online', 'stream.offline'];
        foreach ($types as $type) {
            $response = $this->client->request('POST', 'https://api.twitch.tv/helix/eventsub/subscriptions', [
                'headers' => [
                    'Authorization' => "Bearer {$this->getAccessToken()}",
                    'Client-ID' => $this->id,
                ],
                'json' => [
                    'type' => $type,
                    'version' => '1',
                    'condition' => [
                        'broadcaster_user_id' => '32887598',
                    ],
                    'transport' => [
                        'method' => 'webhook',
                        'callback' => 'https://grafikart.fr/twitch/webhook',
                        'secret' => $this->secret,
                    ],
                ],
            ]);
            if ($response->getStatusCode() >= 300) {
                throw new \Exception("Cannot add twitch subscription :\n" . $response->getContent());
            }
        }
    }

    public function getAccessToken(): string
    {
        $response = $this->client->request('POST', 'https://id.twitch.tv/oauth2/token', [
            'body' => [
                'client_id' => $this->id,
                'client_secret' => $this->secret,
                'grant_type' => 'client_credentials',
            ],
        ]);
        return $response->toArray()['access_token'];
    }

    public function validateSignature(Request $request): bool
    {
        $signature = $request->headers->get('Twitch-Eventsub-Message-Signature');
        $messageId = $request->headers->get('Twitch-Eventsub-Message-Id');
        $timestamp = $request->headers->get('Twitch-Eventsub-Message-Timestamp');
        $content = $request->getContent();
        $hmacMessage = $messageId . $timestamp . $content;
        $expectedSignature = 'sha256=' . hash_hmac('sha256', $hmacMessage, $this->secret);
        return hash_equals($expectedSignature, $signature ?? '');
    }
}
