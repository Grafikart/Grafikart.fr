<?php

namespace App\Infrastructure\Search\Meilisearch;

use App\Infrastructure\Search\SearchInterface;
use App\Infrastructure\Search\SearchResult;

class MeilisearchSearch implements SearchInterface
{
    public function __construct(private readonly MeilisearchClient $client)
    {
    }

    public function search(string $q, array $types = [], int $limit = 50, int $page = 1): SearchResult
    {
        $body = [
            'q' => $q,
            'page' => $page,
            'sort' => ["created_at:desc"],
            'attributesToHighlight' => ['title', 'content'],
            'attributesToCrop' => ['content'],
            'cropLength' => 35,
            'hitsPerPage' => $limit,
        ];
        if (!empty($types)) {
            $body['filter'] = [array_map(fn (string $type) => "type = '$type'", $types)];
        }

        $response = $this->client->post('indexes/content/search', $body);
        $items = $response['hits'];
        return new SearchResult(array_map(fn (array $item) => new MeilisearchItem($item), $items), $response['totalHits'] ?? $response["estimatedTotalHits"]);
    }
}
