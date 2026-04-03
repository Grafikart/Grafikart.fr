<?php

namespace App\Domains\Notification\Subscriber;

use App\Domains\Notification\NotificationService;
use App\Domains\Support\Event\SupportQuestionAnswered;
use Illuminate\Events\Dispatcher;

class NotificationSupportSubscriber
{
    public function __construct(private NotificationService $service) {}

    public function subscribe(Dispatcher $events): array
    {
        return [
            SupportQuestionAnswered::class => 'onAnswered',
        ];
    }

    public function onAnswered(SupportQuestionAnswered $event): void
    {
        $question = $event->question->loadMissing([
            'course:id,title,slug',
            'user:id',
        ]);

        $this->service->send(
            message: "Votre question <strong>{$question->title}</strong> a reçu une réponse.",
            user: $question->user,
            url: app_url($question->course).'#support',
        );
    }
}
