<?php

namespace App\Infrastructure\Notification\Notification;

use App\Domains\Course\Course;
use App\Domains\Course\Formation;
use App\Infrastructure\Notification\Channel\HasSiteNotification;
use App\Infrastructure\Notification\Channel\SiteNotificationChannel;
use App\Infrastructure\Notification\Channel\SiteNotificationMessage;
use Carbon\CarbonInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class ContentPublishedNotification extends Notification implements HasSiteNotification, ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly Course|Formation $content,
    ) {}

    public function via(): string
    {
        return SiteNotificationChannel::class;
    }

    public function toSiteNotification(object $notifiable): SiteNotificationMessage
    {
        $technologies = $this->content->mainTechnologies->pluck('name')->implode(', ');
        $duration = duration($this->content->duration);

        if ($this->content instanceof Course) {
            $message = "Nouveau tutoriel {$technologies} !<br> <strong>{$this->content->title}</strong> <strong>({$duration})</strong>";
        } else {
            $message = "Nouvelle formation {$technologies} disponible :  <strong>{$this->content->title}</strong>";
        }

        return new SiteNotificationMessage(
            url: app_url($this->content),
            message: $message,
            target: $this->content,
        );
    }

    public function withDelay(): ?CarbonInterface
    {
        return $this->content->created_at;
    }
}
