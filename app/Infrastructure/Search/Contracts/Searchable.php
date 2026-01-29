<?php

namespace App\Infrastructure\Search\Contracts;

use App\Infrastructure\Search\SearchDocument;

/**
 * Interface to make a model indexable/searchable in the search engine.
 */
interface Searchable
{
    /**
     * Returns the search document for indexing, or null if the content should not be indexed.
     */
    public function toSearchDocument(): ?SearchDocument;
}
