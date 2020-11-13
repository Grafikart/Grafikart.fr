<?php

namespace App\Infrastructure\Mailing;

use App\Infrastructure\Queue\EnqueueMethod;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Twig\Environment;

class Mailer
{
    private Environment $twig;
    private EnqueueMethod $enqueue;
    private MailerInterface $mailer;

    public function __construct(Environment $twig, EnqueueMethod $enqueue, MailerInterface $mailer)
    {
        $this->twig = $twig;
        $this->enqueue = $enqueue;
        $this->mailer = $mailer;
    }

    public function createEmail(string $template, array $data = []): Email
    {
        $this->twig->addGlobal('format', 'html');
        $html = $this->twig->render($template, array_merge($data, ['layout' => 'mails/base.html.twig']));
        $this->twig->addGlobal('format', 'text');
        $text = $this->twig->render($template, array_merge($data, ['layout' => 'mails/base.text.twig']));

        return (new Email())
            ->from('noreply@grafikart.fr')
            ->html($html)
            ->text($text);
    }

    public function send(Email $email): void
    {
        $this->enqueue->enqueue(MailerInterface::class, 'send', [$email]);
    }

    public function sendNow(Email $email): void
    {
        $this->mailer->send($email);
    }
}
