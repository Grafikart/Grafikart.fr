<?php

namespace App\Console\Commands;

use App\Domains\Support\ContactRequest;
use App\Infrastructure\Notification\NotificationService;
use Illuminate\Console\Command;

class CleanCommand extends Command
{
    protected $signature = 'app:clean';

    protected $description = 'Nettoie les données du site';

    public function handle(
        NotificationService $notificationService
    ) {
        // Clean old notifications
        $count = $notificationService->clean();
        $this->info("{$count} notifications supprimées");

        // Clean old contact requests
        $count = ContactRequest::query()
            ->where('created_at', '<', now()->subDays(7))
            ->delete();
        $this->info("{$count} demandes de contact supprimées");

    }
}
