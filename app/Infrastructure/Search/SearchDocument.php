<?php

namespace App\Infrastructure\Search;

/**
 * Represents an indexable document for the search engine.
 */
readonly class SearchDocument
{
    public function __construct(
        private string $id,
        private string $title,
        private string $content,
        /** @var string[] */
        private array $category,
        private string $type,
        private string $url,
        private int $created_at
    ) {}

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'content' => $this->content,
            'category' => $this->category,
            'type' => $this->type,
            'url' => $this->url,
            'created_at' => $this->created_at,
        ];
    }
}
