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
        foreach ($this->item['highlights'] as $higlight) {
            if ($higlight['field'] === 'content') {
                return $higlight['value'];
            }
        }
        return $this->item['document']['content'];
    }

    public function getUrl(): string
    {
        return $this->item['document']['url'];
    }
}
