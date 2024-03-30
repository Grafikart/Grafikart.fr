<?php

declare(strict_types=1);

namespace App\Http\Controller\Account;

use App\Domain\Auth\User;
use App\Domain\Premium\Repository\TransactionRepository;
use App\Http\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * @method User getUser()
 */
class InvoicesController extends AbstractController
{
    public function __construct(private readonly TransactionRepository $repository)
    {
    }

    #[Route(path: '/profil/factures', name: 'user_invoices', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(): Response
    {
        $transactions = $this->repository->findfor($this->getUser());

        return $this->render('account/invoices.html.twig', [
            'transactions' => $transactions,
            'menu' => 'account',
        ]);
    }

    #[Route(path: '/profil/factures', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function updateInfo(Request $request, EntityManagerInterface $em): RedirectResponse
    {
        $content = (string) $request->request->get('invoiceInfo');
        $user = $this->getUserOrThrow();
        $user->setInvoiceInfo($content);
        $em->flush();
        $this->addFlash('success', 'Vos informations ont bien été enregistrées');

        return $this->redirectToRoute('user_invoices');
    }

    #[Route(path: '/profil/factures/{id<\d+>}', name: 'user_invoice')]
    #[IsGranted('ROLE_USER')]
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
