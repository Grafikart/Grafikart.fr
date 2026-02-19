<?php

namespace App\Http\Cms;

use App\Domains\Notification\NotificationService;
use App\Domains\Premium\TransactionRepository;
use App\Domains\Revision\Revision;
use App\Http\Cms\Data\JobItemData;
use App\Http\Cms\Data\Revision\RevisionRowData;
use App\Infrastructure\Queue\FailedJob;
use App\Infrastructure\Queue\Job;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

final readonly class DashboardController
{
    public function index(TransactionRepository $repository): Response
    {
        return Inertia::render('dashboard', [
            'jobs' => JobItemData::collect(Job::query()->latest()->limit(10)->get()),
            'failedJobs' => JobItemData::collect(FailedJob::query()->latest('failed_at')->limit(10)->get()),
            'days' => $repository->getDailyRevenues(),
            'months' => $repository->getMonthlyRevenues(),
            'revisions' => RevisionRowData::collect(
                Revision::query()->pending()->with(['user', 'revisionable'])->latest()->limit(5)->get()
            ),
        ]);
    }

    public function notification(Request $request, NotificationService $service): JsonResponse
    {
        $user = $request->user();
        assert($user instanceof User);
        $service->send(
            message: 'Ceci est une <strong>notification de test</strong>',
            url: '/',
        );

        return new JsonResponse;
    }
}
