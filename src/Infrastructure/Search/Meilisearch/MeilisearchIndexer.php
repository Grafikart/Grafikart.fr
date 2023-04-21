<?php

namespace App\Infrastructure\Search\Meilisearch;

use App\Infrastructure\Search\IndexerInterface;

class MeilisearchIndexer implements IndexerInterface
{
    public function __construct(private readonly MeilisearchClient $client)
    {
    }

    public function settings(): void
    {
        $this->client->patch('indexes/content/settings', [
            'searchableAttributes' => [
                'title',
                'category',
                'content',
                'url',
            ],
            'sortableAttributes' => ['created_at'],
            'filterableAttributes' => ['type'],
        ]);
    }

    public function index(array $data): void
    {
        $this->client->put('indexes/content/documents', [$data]);
    }

    public function remove(string $id): void
    {
        $this->client->delete("indexes/content/documents/{$id}");
    }

    public function clean(): void
    {
        $this->client->delete('indexes/content/documents');
    }
}
