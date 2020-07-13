<?php

namespace App\Core\Orm;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;

/**
 * Rend une requête iterable.
 *
 * Cette classe permet de passer des requêtes au template sans les éxécuter en amont pour améliorer l'efficacité du cache.
 * La requête n'est pas éxécuté avant la première itération
 */
class IterableQuery implements \IteratorAggregate
{
    private Query $query;

    public function __construct(QueryBuilder $query)
    {
        $this->query = $query->getQuery();
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->query->getResult());
    }
}
