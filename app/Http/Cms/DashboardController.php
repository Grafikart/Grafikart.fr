<?php

namespace App\Http\Cms;

use App\Domains\Premium\TransactionRepository;
use Inertia\Inertia;
use Inertia\Response;

final readonly class DashboardController
{
    public function __invoke(TransactionRepository $repository): Response
    {
        return Inertia::render('dashboard/index', [
            'days' => $repository->getDailyRevenues(),
            'months' => $repository->getMonthlyRevenues(),
        ]);
    }
}
