<?php

namespace App\Helpers;

final class MarkdownHelper
{
    public static function html(string $content): string
    {
        $content = (new CustomParsedown)->setBreaksEnabled(true)->setSafeMode(false)->text($content);
        // On remplace les liens youtube par un embed
        $content = (string) preg_replace_callback(
            '/<p><a href\="(?:http|https):\/\/www.youtube.com\/watch\?v=([^\""]+)">[^<]*<\/a><\/p>/',
            fn (array $matches) => view('components.atoms.lazy-video', ['video' => $matches[1]])->toHtml(),
            (string) $content
        );
        // Spoiler tag
        $content = (string) preg_replace(
            '/<p>!!<\/p>/',
            '<spoiler-box>',
            (string) $content
        );
        $content = (string) preg_replace(
            '/<p>\/!!<\/p>/',
            '</spoiler-box>',
            (string) $content
        );
        // On ajoute des liens sur les nombres représentant un timestamp "00:01"
        $content = preg_replace_callback('/((\d{2}:){1,2}\d{2}) ([^<]*)/', function ($matches) {
            $times = array_reverse(explode(':', $matches[1]));
            $title = $matches[3];
            $timecode = (int) ($times[2] ?? 0) * 60 * 60 + (int) $times[1] * 60 + (int) $times[0];

            return "<a href=\"#t{$timecode}\">{$matches[1]}</a> $title";
        }, $content) ?: $content;

        return $content;
    }

    public static function excerpt(?string $content, int $characterLimit = 135): string
    {
        if (! $content) {
            return '';
        }
        $content = strip_tags(self::html($content));
        if (mb_strlen($content) <= $characterLimit) {
            return $content;
        }
        $lastSpace = strpos($content, ' ', $characterLimit);
        if ($lastSpace === false) {
            return $content;
        }

        return substr($content, 0, $lastSpace).'...';
    }

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
