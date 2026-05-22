<?php

namespace App\Infrastructure\Notification\Notification;

use App\Domains\Support\SupportQuestion;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class SupportQuestionCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly SupportQuestion $question,
    ) {
        $this->afterCommit();
    }

    public function via(): array
    {
        return ['mail'];
    }

    public function toMail(): MailMessage
    {
        $content = trim($this->question->content);

        $message = (new MailMessage)
            ->subject('Grafikart::Support')
            ->greeting('Nouvelle question')
            ->line("Une nouvelle question a été posée sur le tutoriel **{$this->question->course->title}**.")
            ->line("**Question :** {$this->question->title}")
            ->line("**Auteur :** {$this->question->user->name} ({$this->question->user->email})");

        if ($content !== '') {
            $message->line(Str::limit($content, 250));
        }

        return $message->action('Répondre à la question', route('cms.support.edit', $this->question, absolute: true));
    }
}
