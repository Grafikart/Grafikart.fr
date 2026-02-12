<?php

namespace App\Http\Cms;

use App\Domains\Premium\TransactionRepository;
use App\Http\Cms\Data\JobItemData;
use App\Infrastructure\Queue\FailedJob;
use App\Infrastructure\Queue\Job;
use Inertia\Inertia;
use Inertia\Response;

final readonly class DashboardController
{
    public function index(TransactionRepository $repository): Response
    {
        return Inertia::render('dashboard/index', [
            'jobs' => JobItemData::collect(Job::query()->latest()->limit(10)->get()),
            'failedJobs' => JobItemData::collect(FailedJob::query()->latest('failed_at')->limit(10)->get()),
            'days' => $repository->getDailyRevenues(),
            'months' => $repository->getMonthlyRevenues(),
        ]);
    }
}
