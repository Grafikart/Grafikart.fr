<?php

namespace App\Infrastructure\Search\Meilisearch;

use App\Infrastructure\Search\SearchResultItemInterface;

class MeilisearchItem implements SearchResultItemInterface
{
    public function __construct(
        /**
         * An item stored by meilisearch.
         *
         *  [field: string]: string,
         *   _formatted:
         *      {
         *          [field: string]: string,
         *      }
         */
        private readonly array $item,
    ) {
    }

    public function getTitle(): string
    {
        return $this->item['_formatted']['title'];
    }

    public function getExcerpt(): string
    {
        return $this->item['_formatted']['content'];
    }

    public function getUrl(): string
    {
        return $this->item['url'];
    }

    public function getType(): string
    {
        $type = $this->item['type'];
        if ('course' === $type) {
            return 'Tutoriel';
        }
        if ('formation' === $type) {
            return 'Formation';
        }
        if ('post' === $type) {
            return 'Article';
        }

        return $type;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return new \DateTimeImmutable('@'.$this->item['created_at']);
    }

    /**
     * @return string[]
     */
    public function getCategories(): array
    {
        return $this->item['category'];
    }
}
