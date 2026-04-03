<?php

namespace App\Infrastructure\Mailing\Mail;

use App\Domains\Support\SupportQuestion;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SupportQuestionAnsweredMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly SupportQuestion $question,
        public readonly string $url,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address('noreply@grafikart.fr', 'Grafikart'),
            subject: "Grafikart::Réponse à votre question : {$this->question->title}",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.support-question-answered',
            with: [
                'question' => $this->question,
                'url' => $this->url,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
