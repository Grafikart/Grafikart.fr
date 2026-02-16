<?php

namespace App\Infrastructure\Mailing\Mail;

use App\Http\Front\Data\ContactData;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public readonly ContactData $data) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address('noreply@grafikart.fr', 'Grafikart'),
            replyTo: [
                new Address($this->data->email, $this->data->name),
            ],
            subject: "Grafikart::Contact : {$this->data->name}",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.contact',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
