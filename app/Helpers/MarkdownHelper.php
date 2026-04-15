<?php

namespace App\Helpers;

final class MarkdownHelper
{

    /**
     * Convert trusted Markdown to HTML (never use on user-generated content)
     */
    public static function html(?string $content = null): string
    {
        if (! $content) {
            return '';
        }
        $content = (new CustomParsedown)->setBreaksEnabled(true)->setSafeMode(false)->text($content);

        // Transform YouTube link into video player
        $content = (string) preg_replace_callback(
            '/<p><a href\="(?:http|https):\/\/www.youtube.com\/watch\?v=([^\""]+)">[^<]*<\/a><\/p>/',
            fn (array $matches) => view('components.atoms.lazy-video', ['video' => $matches[1]])->toHtml(),
            (string) $content
        );
        // Spoiler tag
        $content = (string) preg_replace(
            '/<p>!!<\/p>/',
            '<spoiler-box>',
            $content
        );
        $content = (string) preg_replace(
            '/<p>\/!!<\/p>/',
            '</spoiler-box>',
            $content
        );

        // Add link on timestamps to jump to a specific time on the parent video "00:01"
        $content = preg_replace_callback('/((\d{2}:){1,2}\d{2}) ([^<]*)/', function ($matches) {
            $times = array_reverse(explode(':', $matches[1]));
            $title = $matches[3];
            $timecode = (int) ($times[2] ?? 0) * 60 * 60 + (int) $times[1] * 60 + (int) $times[0];

            return "<a href=\"#t{$timecode}\">{$matches[1]}</a> $title";
        }, $content) ?: $content;

        return $content;
    }

    /**
     * Create a text excerpt from Markdown content
     */
    public static function excerpt(?string $content, int $characterLimit = 135): string
    {
        if (! $content) {
            return '';
        }

        // Cut the content before the first title (avoid parsing unnecessary content)
        $firstTitlePos = strpos($content, '##');
        if ($firstTitlePos > 50) {
            $content = substr($content, 0, $firstTitlePos);
        }

        // Remove line starting with an url
        $content = preg_replace('/^https?:\/\/.+$/m', '', $content);

        // Generate the text from the Markdown
        $content = strip_tags(self::htmlUntrusted($content));
        if (mb_strlen($content) <= $characterLimit) {
            return $content;
        }
        $lastSpace = strpos($content, ' ', $characterLimit);
        if ($lastSpace === false) {
            return $content;
        }

        return substr($content, 0, $lastSpace).'...';
    }

    /**
     * Convert Markdown to text stripping code blocks (used for search)
     */
    public static function text(?string $content): string
    {
        if (! $content) {
            return '';
        }
        $html = (new \Parsedown)->setSafeMode(false)->parse($content);
        $html = preg_replace('@<pre>.*?</pre>@si', '', (string) $html);
        if (! is_string($html)) {
            return '';
        }

        return strip_tags($html);
    }

    /**
     * Convert untrusted Markdown in HTML
     */
    public static function htmlUntrusted(?string $content): string
    {
        if (! $content) {
            return '';
        }
        $content = new CustomParsedown()
            ->setSafeMode(true)
            ->setBreaksEnabled(true)
            ->text($content);

        $content = str_replace('<a href="http', '<a target="_blank" rel="noreferrer nofollow" href="http', $content);
        $content = str_replace('<a href="//', '<a target="_blank" rel="noreferrer nofollow" href="http', $content);

        return $content;
    }
}
