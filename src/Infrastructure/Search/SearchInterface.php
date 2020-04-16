<?php


namespace App\Infrastructure\Search;


interface SearchInterface
{

    /**
     * @param string $q
     * @param string[] $types
     * @return array
     */
    public function search(string $q, array $types = []): array;

}
