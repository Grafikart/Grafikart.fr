<?php

namespace App\Http\Admin\Controller;

use App\Core\Helper\Paginator\PaginatorInterface;
use App\Domain\Comment\CommentRepository;
use App\Domain\Forum\Repository\ReportRepository;
use App\Domain\Premium\Repository\TransactionRepository;
use App\Domain\Revision\RevisionRepository;
use App\Infrastructure\Queue\FailedJobsService;
use App\Infrastructure\Queue\ScheduledJobsService;
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
}
