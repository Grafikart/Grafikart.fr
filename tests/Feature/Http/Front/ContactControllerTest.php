<?php

use App\Infrastructure\Notification\Mail\ContactMail;
use Illuminate\Support\Facades\Mail;

it('shows the contact form', function () {
    $this->get('/contact')->assertOk();
});

it('sends the contact mail and renders its content', function () {
    Mail::fake();

    $payload = [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'content' => 'Bonjour je fais un test',
    ];

    $this->post('/contact', $payload)
        ->assertRedirect(route('contact'))
        ->assertSessionHas('success');

    Mail::assertSent(ContactMail::class, function (ContactMail $mail) use ($payload) {
        $mail->assertHasSubject("Grafikart::Contact : {$payload['name']}");
        $mail->assertHasReplyTo($payload['email']);
        $mail->assertSeeInText($payload['content']);

        return true;
    });
});
