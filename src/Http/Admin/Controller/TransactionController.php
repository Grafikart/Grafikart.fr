<?php

namespace App\Http\Admin\Controller;

use App\Domain\Premium\Entity\Transaction;
use App\Domain\Premium\Repository\TransactionRepository;
use App\Http\Admin\Data\Transaction\TransactionItemData;
use App\Infrastructure\Payment\Event\PaymentRefundedEvent;
use App\Infrastructure\Payment\Payment;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * @method TransactionRepository getRepository()
 */
#[Route(path: '/transactions', name: 'transaction_')]
final class TransactionController extends InertiaController
{
    protected string $entityClass = Transaction::class;
    protected string $routePrefix = 'transaction';
    protected string $componentDirectory = 'transactions';
    protected string $itemDataClass = TransactionItemData::class;

    #[Route(path: '/', name: 'index')]
    public function index(Request $request): Response
    {
        $search = $request->query->get('q') ?? '';
        $query = $this->getRepository()
            ->createQueryBuilder('row')
            ->leftJoin('row.author', 'author')
            ->addSelect('author')
            ->orderBy('row.createdAt', 'DESC');

        // Filtre les transactions pour un utilisateur donné
        if (str_starts_with($search, 'user:')) {
            $query = $query
                ->where('author.id = :search')
                ->setParameter('search', str_replace('user:', '', $search));
        } elseif (!empty($search)) {
            $query = $query->where('row.methodRef = :search')
                ->orWhere('author.email = :search')
                ->setParameter('search', $search);
        }

        return $this->crudIndex($query);
    }

    #[Route(path: '/{id<\d+>}', name: 'show', methods: ['DELETE'])]
    public function delete(Transaction $transaction, EventDispatcherInterface $dispatcher): Response
    {
        $payment = new Payment();
        $payment->id = (string) $transaction->getMethodRef();
        $dispatcher->dispatch(new PaymentRefundedEvent($payment));
        $this->addFlash('success', 'Le paiement a bien été marqué comme remboursé');

        return $this->redirectBack('admin_transaction_index');
    }
}
