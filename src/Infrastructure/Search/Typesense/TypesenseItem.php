<?php

namespace App\Infrastructure\Search\Typesense;

use App\Infrastructure\Search\SearchResultItem;

class TypesenseItem implements SearchResultItem
{

    /**
     * An item store by typesense
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
        foreach ($this->item['highlights'] as $higlight) {
            if ($higlight['field'] === 'title') {
                return $higlight['value'];
            }
        }
        return $this->item['document']['title'];
    }

    public function getExcerpt(): string
    {
        // Si un extrait est souligné on prend la ligne qui correspond
        foreach ($this->item['highlights'] as $higlight) {
            if ($higlight['field'] === 'content') {
                $lines = preg_split("/((\r?\n)|(\r\n?))/", $higlight['value']);
                if ($lines) {
                    foreach ($lines as $line) {
                        if (strpos($line, '<mark>') !== false) {
                            return $line;
                        }
                    }
                }
                return $higlight['snippet'];
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

    public function getCreatedAt(): \DateTimeInterface
    {
        return new \DateTimeImmutable("@" . $this->item['document']['created_at']);
    }

    /**
     * @return string[]
     */
    public function getCategories(): array
    {
        return $this->item['document']['category'];
    }
}
