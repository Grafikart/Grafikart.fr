<?php

declare(strict_types=1);

namespace App\Http\Controller\Account;

use App\Domain\Auth\User;
use App\Domain\Premium\Repository\TransactionRepository;
use App\Http\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @method User getUser()
 */
class InvoicesController extends AbstractController
{
    private TransactionRepository $repository;

    public function __construct(TransactionRepository $transactionRepository)
    {
        $this->repository = $transactionRepository;
    }

    /**
     * @Route("/profil/factures", name="user_invoices")
     * @IsGranted("ROLE_USER")
     */
    public function index(): Response
    {
        $transactions = $this->repository->findfor($this->getUser());

        return $this->render('account/invoices.html.twig', [
            'transactions' => $transactions,
            'menu' => 'account',
        ]);
    }

    /**
     * @Route("/profil/factures/{id<\d+>}", name="user_invoice")
     * @IsGranted("ROLE_USER")
     */
    public function show(int $id): Response
    {
        $transaction = $this->repository->findOneBy([
            'id' => $id,
            'author' => $this->getUser(),
        ]);

        if (null === $transaction) {
            throw new NotFoundHttpException();
        }

        return $this->render('account/invoice.html.twig', [
            'transaction' => $transaction,
        ]);
    }
}
