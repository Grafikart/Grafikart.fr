<?php

namespace App\Infrastructure\Search;

interface SearchResultItem
{
    public function getTitle(): string;
    public function getExcerpt(): string;
    public function getUrl(): string;
}
