<?php

namespace App\Http\Admin\Controller;

use App\Domain\Comment\CommentRepository;
use App\Domain\Forum\Repository\ReportRepository;
use App\Domain\Premium\Repository\TransactionRepository;
use App\Domain\Revision\RevisionRepository;
use App\Helper\Paginator\PaginatorInterface;
use App\Infrastructure\Mailing\Mailer;
use App\Infrastructure\Queue\FailedJobsService;
use App\Infrastructure\Queue\ScheduledJobsService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class PageController extends BaseController
{
    /**
     * @Route("", name="home")
     */
    public function index(
        PaginatorInterface $paginator,
        RevisionRepository $revisionRepository,
        CommentRepository $commentRepository,
        ReportRepository $reportRepository,
        FailedJobsService $failedJobsService,
        TransactionRepository $transactionRepository,
        ScheduledJobsService $scheduledJobsService
    ): Response {
        return $this->render('admin/home.html.twig', [
            'revisions' => $revisionRepository->findLatest(),
            'comments' => $paginator->paginate($commentRepository->queryLatest()),
            'reports' => $reportRepository->findAll(),
            'menu' => 'home',
            'failed_jobs' => $failedJobsService->getJobs(),
            'months' => $transactionRepository->getMonthlyRevenues(),
            'days' => $transactionRepository->getDailyRevenues(),
            'scheduled_jobs' => $scheduledJobsService->getJobs(),
        ]);
    }

    /**
     * @Route("/stats", name="stats")
     */
    public function stats(): Response
    {
        return $this->render('admin/page/stats.html.twig');
    }

    /**
     * Envoie un email de test à mail-tester pour vérifier la configuration du serveur.
     *
     * @Route("/mailtester", name="mailtest", methods={"POST"})
     */
    public function testMail(Request $request, Mailer $mailer): RedirectResponse
    {
        $email = $mailer->createEmail('mails/auth/register.twig', [
            'user' => $this->getUserOrThrow(),
        ])
            ->to($request->get('email'))
            ->subject('Grafikart | Confirmation du compte');
        $mailer->sendNow($email);
        $this->addFlash('success', "L'email de test a bien été envoyé");

        return $this->redirectToRoute('admin_home');
    }
}
