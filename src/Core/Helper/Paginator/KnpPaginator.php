<?php

namespace App\Core\Helper\Paginator;

use Doctrine\ORM\Query;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Implémentation basée sur KnpPaginatorBundle.
 */
class KnpPaginator implements PaginatorInterface
{
    private \Knp\Component\Pager\PaginatorInterface $paginator;
    private RequestStack $requestStack;
    private array $sortableFields = [];

    public function __construct(\Knp\Component\Pager\PaginatorInterface $paginator,
RequestStack $requestStack)
    {
        $this->paginator = $paginator;
        $this->requestStack = $requestStack;
    }

    public function paginate(Query $query): iterable
    {
        $request = $this->requestStack->getCurrentRequest();
        $page = $request ? $request->query->getInt('page', 1) : 1;

        return $this->paginator->paginate($query, $page, $query->getMaxResults() ?: 15, [
            'sortFieldWhitelist' => $this->sortableFields,
            'filterFieldWhitelist' => [],
        ]);
    }

    public function allowSort(string ...$fields): self
    {
        $this->sortableFields = array_merge($this->sortableFields, $fields);

        return $this;
    }
}
