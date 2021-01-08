<?php

namespace App\Infrastructure\Search\Typesense;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class TypesenseClient
{
    private string $host;
    private string $apiKey;
    private HttpClientInterface $client;

    public function __construct(string $host, string $apiKey, HttpClientInterface $client)
    {
        if (empty($apiKey)) {
            throw new \RuntimeException("Une clef d'API est nécessaire à l'utilisation de typesense");
        }
        $this->host = $host;
        $this->apiKey = $apiKey;
        $this->client = $client;
    }

    public function get(string $endpoint): array
    {
        return $this->api($endpoint, [], 'GET');
    }

    public function post(string $endpoint, array $data = []): array
    {
        return $this->api($endpoint, $data, 'POST');
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
                'X-TYPESENSE-API-KEY' => $this->apiKey,
            ],
        ]);
        if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
            return json_decode($response->getContent(), true);
        }
        throw new TypesenseException($response);
    }
}
