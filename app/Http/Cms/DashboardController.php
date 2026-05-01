<?php

namespace App\Http\Cms;

use App\Domains\Premium\TransactionRepository;
use App\Domains\Revision\Revision;
use App\Domains\Support\SupportQuestion;
use App\Http\Cms\Data\JobItemData;
use App\Http\Cms\Data\Revision\RevisionRowData;
use App\Http\Cms\Data\Support\SupportQuestionRowData;
use App\Infrastructure\Notification\Notification\TestNotification;
use App\Infrastructure\Notification\Notification\UserDeletionNotification;
use App\Infrastructure\Queue\FailedJob;
use App\Infrastructure\Queue\Job;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Notification;
use Inertia\Inertia;
use Inertia\Response;

final readonly class DashboardController
{
    /**
     * Dashboard with the last events happening on the site
     */
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

    /**
     * Send a fake notification for the current user signed in
     */
    public function notification(Request $request): JsonResponse
    {
        $user = $request->user();
        assert($user instanceof User);
        $user->notify(new TestNotification);

        return new JsonResponse;
    }

    /**
     * Send a fake email to test the email setup (DKIM signature, DNS records...)
     */
    public function emailTest(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
        ]);

        Notification::route('mail', $data['email'])
            ->notify(new UserDeletionNotification($request->user(), 'Email de test'));

        return back()->with('success', "Email de test envoyé à {$data['email']}");
    }

    public function clearCache(): RedirectResponse
    {
        Artisan::call('cache:clear');

        return back()->with('success', 'Le cache a bien été vidé');
    }
}
