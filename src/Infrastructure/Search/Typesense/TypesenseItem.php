<?php

namespace App\Infrastructure\Search\Typesense;

use App\Infrastructure\Search\SearchResultItemInterface;

class TypesenseItem implements SearchResultItemInterface
{
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
    private array $item;

    public function __construct(array $item)
    {
        $this->item = $item;
    }

    public function getTitle(): string
    {
        foreach ($this->item['highlights'] as $highlight) {
            if ('title' === $highlight['field']) {
                return $highlight['value'];
            }
        }

        return $this->item['document']['title'];
    }

    public function getExcerpt(): string
    {
        // Si un extrait est souligné on prend la ligne qui correspond
        foreach ($this->item['highlights'] as $highlight) {
            if ('content' === $highlight['field']) {
                $lines = preg_split("/((\r?\n)|(\r\n?)|(\.\s))/", $highlight['value']);
                if ($lines) {
                    foreach ($lines as $line) {
                        if (false !== strpos($line, '<mark>')) {
                            return $line;
                        }
                    }
                }

                return $highlight['snippet'];
            }
        }

        // Sinon on coupe les X premiers aaractères
        $content = $this->item['document']['content'];
        $characterLimit = 150;
        if (mb_strlen($content) <= $characterLimit) {
            return $content;
        }
        $lastSpace = strpos($content, ' ', $characterLimit);
        if (false === $lastSpace) {
            return $content;
        }

        return substr($content, 0, $lastSpace).'...';
    }

    public function getUrl(): string
    {
        return $this->item['document']['url'];
    }

    public function getType(): string
    {
        $type = $this->item['document']['type'];
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
        return new \DateTimeImmutable('@'.$this->item['document']['created_at']);
    }

    /**
     * @return string[]
     */
    public function getCategories(): array
    {
        return $this->item['document']['category'];
    }
}
