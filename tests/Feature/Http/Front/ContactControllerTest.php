<?php

use App\Infrastructure\Notification\Mail\ContactMail;
use Illuminate\Support\Facades\Mail;

it('shows the contact form', function () {
    $this->get('/contact')->assertOk();
});

it('validates the email field', function (string $email) {
    $this->post('/contact', [
        'name' => 'John Doe',
        'email' => $email,
        'content' => 'Bonjour je fais un test pour voir si cela fonctionne pour vérifier',
    ])->assertSessionHasErrors('email');
})->with([
    'empty' => '',
    'invalid format' => 'not-an-email',
    'missing domain' => 'john@',
]);

it('sends the contact mail and renders its content', function () {
    Mail::fake();

    $payload = [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'content' => 'Bonjour je fais un test pour voir si cela fonctionne pour vérifier',
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
