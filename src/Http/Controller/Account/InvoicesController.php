<?php

declare(strict_types=1);

namespace App\Http\Controller\Account;

use App\Domain\Auth\User;
use App\Domain\Premium\Repository\TransactionRepository;
use App\Http\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
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
     * @Route("/profil/factures", name="user_invoices", methods={"GET"})
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
     * @Route("/profil/factures", methods={"POST"})
     * @IsGranted("ROLE_USER")
     */
    public function updateInfo(Request $request, EntityManagerInterface $em): RedirectResponse
    {
        $content = $request->request->get('invoiceInfo');
        $user = $this->getUserOrThrow();
        $user->setInvoiceInfo($content);
        $em->flush();
        $this->addFlash('success', 'Vos informations ont bien été enregistrées');

        return $this->redirectToRoute('user_invoices');
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
