<?php

namespace App\Http\Cms;

use App\Infrastructure\Queue\FailedJob;
use App\Infrastructure\Queue\Job;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Artisan;

final readonly class JobController
{
    public function destroy(Job $job): RedirectResponse
    {
        $job->delete();

        return back()->with('success', 'Le job a bien été supprimé');
    }

    public function destroyFailed(FailedJob $job): RedirectResponse
    {
        $job->delete();

        return back()->with('success', 'Le job échoué a bien été supprimé');
    }

    public function retryFailed(FailedJob $job): RedirectResponse
    {
        Artisan::call('queue:retry', [$job->uuid]);
        $job->delete();

        return back()->with('success', 'Le job a bien été relancé');
    }

    public function flushFailed(): RedirectResponse
    {
        Artisan::call('queue:flush');

        return back()->with('success', 'Les jobs échoués ont bien été supprimés');
    }
}
