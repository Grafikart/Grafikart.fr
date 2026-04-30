<?php

namespace App\Infrastructure\Notification\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TestMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly string $fakeSubject,
        public readonly string $fakeBody,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: $this->fakeSubject);
    }

    public function build(): self
    {
        return $this->html(nl2br(e($this->fakeBody)));
    }
}
