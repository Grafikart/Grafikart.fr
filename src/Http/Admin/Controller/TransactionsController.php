<?php

namespace App\Http\Admin\Controller;

use App\Domain\Premium\Entity\Transaction;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/transactions", name="transaction_")
 *
 * @method getRepository() App\Domain\
 */
final class TransactionsController extends CrudController
{
    protected string $templatePath = 'transactions';
    protected string $menuItem = 'transactions';
    protected string $entity = Transaction::class;
    protected string $routePrefix = 'admin_transaction';
    protected string $searchField = 'methodRef';

    /**
     * @Route("/", name="index")
     */
    public function index(): Response
    {
        return $this->crudIndex();
    }

    public function applySearch(string $search, QueryBuilder $query): QueryBuilder
    {
        return $query->where('row.methodRef = :search')
            ->orWhere('u.email = :search')
            ->leftJoin('row.author', 'u')
            ->setParameter('search', $search);
    }
}
