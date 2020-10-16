<?php

namespace App\Infrastructure\Search\Typesense;

use App\Infrastructure\Search\SearchInterface;
use App\Infrastructure\Search\SearchResult;
use function GuzzleHttp\Psr7\build_query;

class TypesenseSearch implements SearchInterface
{
    private TypesenseClient $client;

    public function __construct(TypesenseClient $client)
    {
        $this->client = $client;
    }

    public function search(string $q, array $types = [], int $limit = 50, int $page = 1): SearchResult
    {
        $query = [
            'q' => $q,
            'page' => $page,
            'query_by' => 'title,category,content',
            'highlight_full_fields' => 'content,title',
            'per_page' => $limit,
            'num_typos' => 1,
        ];
        if (!empty($types)) {
            $query['filter_by'] = 'type:['.implode(',', $types).']';
        }

        ['found' => $found, 'hits' => $items] = $this->client->get('collections/content/documents/search?'.build_query($query));

        return new SearchResult(array_map(fn (array $item) => new TypesenseItem($item), $items), $found);
    }
}
