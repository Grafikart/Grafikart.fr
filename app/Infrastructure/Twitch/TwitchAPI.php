<?php

namespace App\Infrastructure\Twitch;

use Exception;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

/**
 * @phpstan-type Subscription array{id: string, status: string, type: string, version: string, condition: array<string, mixed>, transport: array<string, mixed>, created_at: string}
 */
class TwitchAPI
{
    public function __construct(
        private readonly string $id,
        private readonly string $secret,
    ) {}

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getSubscriptions(): array
    {
        $response = $this->client()->get('https://api.twitch.tv/helix/eventsub/subscriptions');

        return $response->json('data');
    }

    public function addWebhookSubscription(): void
    {
        $types = ['stream.online', 'stream.offline'];
        foreach ($types as $type) {
            $response = $this->client()->post('https://api.twitch.tv/helix/eventsub/subscriptions', [
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
            ]);
            if ($response->failed()) {
                throw new Exception("Cannot add twitch subscription:\n".$response->body());
            }
        }
    }

    public function getAccessToken(): string
    {
        $response = Http::asForm()->post('https://id.twitch.tv/oauth2/token', [
            'client_id' => $this->id,
            'client_secret' => $this->secret,
            'grant_type' => 'client_credentials',
        ]);

        return $response->json('access_token');
    }

    public function validateSignature(Request $request): bool
    {
        $signature = $request->header('Twitch-Eventsub-Message-Signature');
        $messageId = $request->header('Twitch-Eventsub-Message-Id');
        $timestamp = $request->header('Twitch-Eventsub-Message-Timestamp');
        $content = $request->getContent();
        $hmacMessage = $messageId.$timestamp.$content;
        $expectedSignature = 'sha256='.hash_hmac('sha256', $hmacMessage, $this->secret);

        return hash_equals($expectedSignature, $signature ?? '');
    }

    private function client(): PendingRequest
    {
        return Http::withHeaders([
            'Authorization' => "Bearer {$this->getAccessToken()}",
            'Client-ID' => $this->id,
        ]);
    }
}
