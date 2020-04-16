<?php

namespace App\Infrastructure\Search;

/**
 * Représente un document indexable par le système de recherche
 */
class SearchDocument
{

    public string $title;

    public string $content;

    /**
     * @var string[]
     */
    public array $category;

    public string $type;

    public int $created_at;

}
