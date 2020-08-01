<?php

namespace App\Infrastructure\Search;

interface SearchInterface
{
    /**
     * @param string|null $q
     * @param string[]    $types
     */
    public function search(?string $q, array $types = []): array;
}
