<?php

namespace App\Http\Admin\Controller;

use App\Infrastructure\Queue\FailedJobsService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

class JobsController extends BaseController
{
    private FailedJobsService $failedJobsService;

    public function __construct(FailedJobsService $failedJobsService)
    {
        $this->failedJobsService = $failedJobsService;
    }

    /**
     * @Route("/{id}", methods={"DELETE"}, name="job_delete")
     */
    public function delete(int $id): RedirectResponse
    {
        $this->failedJobsService->deleteJob($id);
        $this->addFlash('success', 'La tâche a bien été supprimée');

        return $this->redirectToRoute('admin_home');
    }

    /**
     * @Route("/{id}/retry", methods={"POST"}, name="job_retry")
     */
    public function retry(int $id): RedirectResponse
    {
        $this->failedJobsService->retryJob($id);
        $this->addFlash('success', 'La tâche a bien été relancée');

        return $this->redirectToRoute('admin_home');
    }
}
