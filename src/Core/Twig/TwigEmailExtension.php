<?php

namespace App\Core\Twig;

use Parsedown;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class TwigEmailExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('markdown_email', [$this, 'markdownEmail'], [
                'needs_context' => true,
                'is_safe' => ['html'],
            ]),
            new TwigFilter('text_email', [$this, 'formatText']),
        ];
    }

    /**
     * Convertit le contenu markdown en HTML.
     */
    public function markdownEmail(array $context, string $content): string
    {
        if (($context['format'] ?? 'text') === 'text') {
            return $content;
        }
        $content = preg_replace('/^(^ {2,})(\S+[ \S]*)$/m', '${2}', $content);
        $content = (new Parsedown())->setSafeMode(false)->text($content);

        return $content;
    }

    public function formatText(string $content): string
    {
        $content = strip_tags($content);
        $content = preg_replace('/^(^ {2,})(\S+[ \S]*)$/m', '${2}', $content) ?: '';
        $content = preg_replace("/([\r\n] *){3,}/", "\n\n", $content) ?: '';

        return $content;
    }
}
