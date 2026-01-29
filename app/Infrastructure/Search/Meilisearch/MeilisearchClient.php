<?php

namespace App\Infrastructure\Search\Meilisearch;

use Illuminate\Http\Client\Factory;

class MeilisearchClient
{
    public function __construct(
        private readonly string $endpoint,
        private readonly string $apiKey,
        private readonly Factory $http,
    ) {}

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
        $request = $this->http->asJson();

        if (! empty($this->apiKey)) {
            $request = $request->withToken($this->apiKey);
        }

        $response = $request->send($method, "{$this->endpoint}/{$endpoint}", ['json' => $data]);

        if ($response->successful()) {
            return $response->json();
        }

        throw new MeilisearchException($response);
    }
}
