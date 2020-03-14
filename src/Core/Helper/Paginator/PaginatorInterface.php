<?php

namespace App\Core\Helper\Paginator;

use Doctrine\ORM\Query;

/**
 * Interface pour la pagination permettant de faire le lien avec la librairie externe
 */
interface PaginatorInterface
{

    public function allowSort(string ...$fields): self;

    public function paginate(Query $query): iterable;

}
