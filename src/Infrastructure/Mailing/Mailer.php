<?php

namespace App\Infrastructure\Mailing;

use App\Infrastructure\Queue\EnqueueMethod;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Crypto\DkimSigner;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Message;
use Twig\Environment;

class Mailer
{
    private Environment $twig;
    private EnqueueMethod $enqueue;
    private MailerInterface $mailer;
    private ?string $dkimKey;

    public function __construct(
        Environment $twig,
        EnqueueMethod $enqueue,
        MailerInterface $mailer,
        ?string $dkimKey = null
    ) {
        $this->twig = $twig;
        $this->enqueue = $enqueue;
        $this->mailer = $mailer;
        $this->dkimKey = $dkimKey;
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
        $this->enqueue->enqueue(self::class, 'sendNow', [$email]);
    }

    public function sendNow(Email $email): void
    {
        if ($this->dkimKey) {
            $dkimSigner = new DkimSigner("file://{$this->dkimKey}", 'grafikart.fr', 'default');
            // On signe un message en attendant le fix https://github.com/symfony/symfony/issues/40131
            $message = new Message($email->getPreparedHeaders(), $email->getBody());
            $email = $dkimSigner->sign($message, []);
        }
        $this->mailer->send($email);
    }
}
