<?php

namespace App\Infrastructure\Mailing\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UserDeletedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public readonly User $user, public readonly string $reason) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address('noreply@grafikart.fr', 'Grafikart'),
            subject: "Grafikart::Suppression de compte : {$this->user->name}",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.user-deleted',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
