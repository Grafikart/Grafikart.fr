<?php

namespace App\Infrastructure\Search\Typesense;

use App\Infrastructure\Search\SearchInterface;
use App\Infrastructure\Search\SearchResult;
use GuzzleHttp\Psr7\Query;

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
            'highlight_affix_num_tokens' => 4,
            'per_page' => $limit,
            'num_typos' => 1,
        ];
        if (!empty($types)) {
            $query['filter_by'] = 'type:['.implode(',', $types).']';
        }

        ['found' => $found, 'hits' => $items] = $this->client->get('collections/content/documents/search?'.Query::build($query));

        return new SearchResult(array_map(fn (array $item) => new TypesenseItem($item), $items), $found > 10 * $limit ? 10 * $limit : $found);
    }
}
