<?php

namespace App\Infrastructure\Search\Typesense;

use App\Infrastructure\Search\SearchInterface;
use function GuzzleHttp\Psr7\build_query;

class TypesenseSearch implements SearchInterface
{

    private TypesenseClient $client;

    public function __construct(TypesenseClient $client)
    {
        $this->client = $client;
    }

    public function search(string $q, array $types = []): array
    {
        $query = [
            'q' => $q,
            'query_by' => 'title,category,content',
            'per_page' => 50,
            'num_typos' => 1
        ];
        if (!empty($types)) {
            $query['filter_by'] = 'type:[' . implode(',', $types) . ']';
        }
        return $this->client->get("collections/content/documents/search?" . build_query($query));
    }
}
