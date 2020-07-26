<?php

namespace App\Infrastructure\Search;

interface SearchInterface
{
    /**
     * @param string[] $types
     */
    public function search(string $q, array $types = []): array;
}
