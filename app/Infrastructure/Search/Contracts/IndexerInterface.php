<?php

namespace App\Infrastructure\Search\Contracts;

interface IndexerInterface
{
    /**
     * Index content in the search engine.
     *
     * @param  array  $data  {id: string, title: string, content: string, created_at: int, category: string[]}
     */
    public function index(array $data): void;

    /**
     * Remove content from the index.
     */
    public function remove(string $id): void;

    /**
     * Clear all data from the index.
     */
    public function clean(): void;
}
