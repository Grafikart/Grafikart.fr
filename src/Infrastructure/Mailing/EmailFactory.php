<?php

namespace App\Infrastructure\Mailing;

use Symfony\Component\Mime\Email;
use Twig\Environment;

final class EmailFactory
{
    private Environment $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * @param array<string,mixed> $data
     */
    public function makeFromTemplate(string $template, array $data = []): Email
    {
        return (new Email())
            ->from('noreply@grafikart.fr')
            ->html($this->twig->render($template, array_merge($data, ['format' => 'html'])))
            ->text($this->twig->render($template, array_merge($data, ['format' => 'text'])));
    }
}
