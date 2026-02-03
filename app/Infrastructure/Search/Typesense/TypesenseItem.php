<?php

namespace App\Infrastructure\Search\Typesense;

use App\Infrastructure\Search\Contracts\SearchResultItemInterface;
use Carbon\CarbonImmutable;

class TypesenseItem implements SearchResultItemInterface
{
    public function __construct(
        /**
         * An item store by typesense.
         *
         *  {
         *    document: {
         *      field: 'value',
         *      field2: 'value',
         *   },
         *   highlights:[
         *      {
         *          field:"title",
         *          snippet: "an excerpt with <mark>",
         *          value: "the whole string with <mark>",
         *      }
         */
        private readonly array $item,
    ) {}

    public function getTitle(): string
    {
        foreach ($this->item['highlights'] as $highlight) {
            if ($highlight['field'] === 'title') {
                return $highlight['value'];
            }
        }

        return $this->item['document']['title'];
    }

    public function getExcerpt(): string
    {
        // Si un extrait est souligné on prend la ligne qui correspond
        foreach ($this->item['highlights'] as $highlight) {
            if ($highlight['field'] === 'content') {
                $lines = preg_split("/((\r?\n)|(\r\n?)|(\.\s))/", (string) $highlight['value']);
                if ($lines) {
                    foreach ($lines as $line) {
                        if (str_contains($line, '<mark>')) {
                            return $line;
                        }
                    }
                }

                return $highlight['snippet'];
            }
        }

        // Sinon on coupe les X premiers caractères
        $content = $this->item['document']['content'];
        $characterLimit = 150;
        if (mb_strlen((string) $content) <= $characterLimit) {
            return $content;
        }
        $lastSpace = strpos((string) $content, ' ', $characterLimit);
        if ($lastSpace === false) {
            return $content;
        }

        return substr((string) $content, 0, $lastSpace).'...';
    }

    public function getUrl(): string
    {
        return $this->item['document']['url'];
    }

    public function getType(): string
    {
        $type = $this->item['document']['type'];
        if ($type === 'course') {
            return 'Tutoriel';
        }
        if ($type === 'formation') {
            return 'Formation';
        }
        if ($type === 'post') {
            return 'Article';
        }

        return $type;
    }

    public function getCreatedAt(): CarbonImmutable
    {
        return new CarbonImmutable('@'.$this->item['document']['created_at']);
    }

    /**
     * @return string[]
     */
    public function getCategories(): array
    {
        return $this->item['document']['category'];
    }
}
