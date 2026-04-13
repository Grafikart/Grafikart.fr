<?php

namespace App\Http\Cms;

use App\Domains\Premium\TransactionRepository;
use App\Domains\Revision\Revision;
use App\Domains\Support\SupportQuestion;
use App\Http\Cms\Data\JobItemData;
use App\Http\Cms\Data\Revision\RevisionRowData;
use App\Http\Cms\Data\Support\SupportQuestionRowData;
use App\Infrastructure\Notification\Notification\TestNotification;
use App\Infrastructure\Notification\NotificationService;
use App\Infrastructure\Queue\FailedJob;
use App\Infrastructure\Queue\Job;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
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
            'supportQuestions' => SupportQuestionRowData::collect(
                SupportQuestion::query()
                    ->with(['user:id,name', 'course:id,title'])
                    ->whereNull('answer')
                    ->orderByDesc('id')
                    ->limit(5)
                    ->get()
            ),
        ]);
    }

    public function notification(Request $request, NotificationService $service): JsonResponse
    {
        $user = $request->user();
        assert($user instanceof User);
        $user->notify(new TestNotification);

        return new JsonResponse;
    }

    public function clearCache(): RedirectResponse
    {
        Artisan::call('cache:clear');

        return back()->with('success', 'Le cache a bien été vidé');
    }
}
