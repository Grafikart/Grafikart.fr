<?php

namespace App\Http\Admin\Controller;

use App\Domain\Premium\Entity\Transaction;
use App\Domain\Premium\Repository\TransactionRepository;
use App\Infrastructure\Payment\Event\PaymentRefundedEvent;
use App\Infrastructure\Payment\Payment;
use Doctrine\ORM\QueryBuilder;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @method getRepository() App\Domain\
 */
#[Route(path: '/transactions', name: 'transaction_')]
final class TransactionsController extends CrudController
{
    protected string $templatePath = 'transactions';
    protected string $menuItem = 'transaction';
    protected string $entity = Transaction::class;
    protected string $routePrefix = 'admin_transaction';
    protected string $searchField = 'methodRef';

    #[Route(path: '/', name: 'index')]
    public function index(): Response
    {
        return $this->crudIndex();
    }

    #[Route(path: '/transaction/{id<\d+>}', name: 'show', methods: ['DELETE'])]
    public function delete(Transaction $transaction, EventDispatcherInterface $dispatcher): Response
    {
        $payment = new Payment();
        $payment->id = (string) $transaction->getMethodRef();
        $dispatcher->dispatch(new PaymentRefundedEvent($payment));
        $this->addFlash('success', 'Le paiement a bien été marqué comme remboursé');

        return $this->redirectBack('admin_transaction_index');
    }

    #[Route(path: '/transaction/report', name: 'report', methods: ['GET'])]
    public function report(TransactionRepository $repository, Request $request): Response
    {
        $year = $request->query->getInt('year', (int) date('Y'));

        return $this->render('admin/transactions/report.html.twig', [
            'reports' => $repository->getMonthlyReport($year),
            'prefix' => 'admin_transaction',
            'current_year' => date('Y'),
            'year' => $year,
        ]);
    }

    public function applySearch(string $search, QueryBuilder $query): QueryBuilder
    {
        $query = $query->leftJoin('row.author', 'u');

        // Filtre les transaction pour un utilisateur donné
        if (str_starts_with($search, 'user:')) {
            return $query
                ->where('u.id = :search')
                ->setParameter('search', str_replace('user:', '', $search));
        }

        // Recherche classique
        return $query->where('row.methodRef = :search')
            ->orWhere('u.email = :search')
            ->setParameter('search', $search);
    }
}
