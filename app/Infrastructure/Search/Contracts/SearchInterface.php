<?php

namespace App\Infrastructure\Search\Contracts;

use App\Infrastructure\Search\SearchResult;

interface SearchInterface
{
    /**
     * @param  string[]  $types
     */
    public function search(string $q, array $types = [], int $limit = 50, int $page = 1): SearchResult;
}
