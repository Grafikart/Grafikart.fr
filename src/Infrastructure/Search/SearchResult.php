<?php

namespace App\Infrastructure\Search;

class SearchResult
{
    /**
     * @param SearchResultItemInterface[] $items
     */
    public function __construct(private readonly array $items, private readonly int $total)
    {
    }

    /**
     * @return SearchResultItemInterface[]
     */
    public function getItems(): array
    {
        return $this->items;
    }

    public function getTotal(): int
    {
        return $this->total;
    }
}
