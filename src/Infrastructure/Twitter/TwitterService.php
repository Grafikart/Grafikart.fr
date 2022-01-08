<?php

namespace App\Infrastructure\Twitter;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class TwitterService
{
    public function __construct(
        private readonly string $apiKey,
        private readonly string $apiSecret,
        private readonly HttpClientInterface $http
    ) {
    }

    /**
     * @return Tweet[]
     */
    public function getLastTweets(): array
    {
        try {
            $token = $this->getBearerToken();
            $response = $this->http->request(
                'GET',
                'https://api.twitter.com/1.1/statuses/user_timeline.json',
                [
                    'headers' => [
                        'authorization' => "Bearer $token",
                    ],
                    'query'   => [
                        'screen_name' => 'grafikart_fr',
                        'count' => 3,
                        'exclude_replies' => true,
                    ],
                ]
            );
            $tweets = array_map(fn(array $tweet) => new Tweet($tweet), $response->toArray());

            return $tweets;
        } catch (\Exception) {
            return [];
        }
    }

    private function getBearerToken(): string
    {
        if (empty($this->apiKey) || empty($this->apiSecret)) {
            throw new \Exception('Twitter API credentials are not set');
        }
        $response = $this->http->request(
            'POST',
            'https://api.twitter.com/oauth2/token',
            [
                'auth_basic' => [$this->apiKey, $this->apiSecret],
                'body'       => 'grant_type=client_credentials',
            ]
        );

        return $response->toArray()['access_token'];
    }
}
