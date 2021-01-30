<?php

namespace App\Helper\Paginator;

use Doctrine\ORM\Query;
use Knp\Component\Pager\Pagination\PaginationInterface;

/**
 * Interface pour la pagination permettant de faire le lien avec la librairie externe.
 */
interface PaginatorInterface
{
    public function allowSort(string ...$fields): self;

    public function paginate(Query $query): PaginationInterface;
}
