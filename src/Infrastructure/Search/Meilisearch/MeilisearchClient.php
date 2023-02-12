<?php

namespace App\Infrastructure\Search\Meilisearch;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class MeilisearchClient
{
    private readonly string $apiKey;

    public function __construct(
        private readonly string $host,
        string $apiKey,
        private readonly HttpClientInterface $client
    ) {
        if (empty($apiKey)) {
            throw new \RuntimeException("Une clef d'API est nécessaire à l'utilisation de meilisearch");
        }
        $this->apiKey = $apiKey;
    }

    public function get(string $endpoint): array
    {
        return $this->api($endpoint, [], 'GET');
    }

    public function post(string $endpoint, array $data = []): array
    {
        return $this->api($endpoint, $data, 'POST');
    }

    public function put(string $endpoint, array $data = []): array
    {
        return $this->api($endpoint, $data, 'PUT');
    }

    public function patch(string $endpoint, array $data = []): array
    {
        return $this->api($endpoint, $data, 'PATCH');
    }


    public function delete(string $endpoint, array $data = []): array
    {
        return $this->api($endpoint, $data, 'DELETE');
    }

    private function api(string $endpoint, array $data = [], string $method = 'POST'): array
    {
        $response = $this->client->request($method, "http://{$this->host}/{$endpoint}", [
            'json' => $data,
            'headers' => [
                'Authorization' => "Bearer " . $this->apiKey,
            ],
        ]);
        if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
            return json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);
        }
        throw new MeilisearchException($response);
    }
}
