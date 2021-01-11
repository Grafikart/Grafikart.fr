<?php

namespace App\Http\Admin\Controller;

use App\Infrastructure\Queue\FailedJobsService;
use App\Infrastructure\Queue\ScheduledJobsService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class JobsController extends BaseController
{
    private FailedJobsService $failedJobsService;
    private ScheduledJobsService $scheduledJobsService;

    public function __construct(FailedJobsService $failedJobsService, ScheduledJobsService $scheduledJobsService)
    {
        $this->failedJobsService = $failedJobsService;
        $this->scheduledJobsService = $scheduledJobsService;
    }

    /**
     * @Route("/{id<\d+>}", methods={"DELETE"}, name="job_delete")
     */
    public function delete(int $id, Request $request): RedirectResponse
    {
        if ($request->get('delayed')) {
            $this->scheduledJobsService->deleteJob($id);
        } else {
            $this->failedJobsService->deleteJob($id);
        }
        $this->addFlash('success', 'La tâche a bien été supprimée');

        return $this->redirectToRoute('admin_home');
    }

    /**
     * @Route("/{id<\d+>}/retry", methods={"POST"}, name="job_retry")
     */
    public function retry(int $id): RedirectResponse
    {
        $this->failedJobsService->retryJob($id);
        $this->addFlash('success', 'La tâche a bien été relancée');

        return $this->redirectToRoute('admin_home');
    }
}
