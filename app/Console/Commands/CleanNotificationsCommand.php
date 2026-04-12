<?php

namespace App\Console\Commands;

use App\Infrastructure\Notification\NotificationService;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\Isolatable;

class CleanNotificationsCommand extends Command implements Isolatable
{
    protected $signature = 'app:clean-notifications';

    protected $description = 'Delete notifications older than 6 months';

    public function handle(NotificationService $service): int
    {
        $count = $service->clean();

        $this->info("Deleted {$count} old notifications.");

        return self::SUCCESS;
    }
}
