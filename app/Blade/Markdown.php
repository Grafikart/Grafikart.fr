<?php

namespace App\Blade;

final class Markdown
{
    public static function toHtml(string $content)
    {
        $content = (new \Parsedown)->setBreaksEnabled(true)->setSafeMode(false)->text($content);
        // On remplace les liens youtube par un embed
        $content = (string) preg_replace(
            '/<p><a href\="(http|https):\/\/www.youtube.com\/watch\?v=([^\""]+)">[^<]*<\/a><\/p>/',
            '<lazy-video videoid="$2"></lazy-video>',
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
        if (!$content) {
            return '';
        }
        $content = strip_tags(self::toHtml($content));
        if (mb_strlen($content) <= $characterLimit) {
            return $content;
        }
        $lastSpace = strpos($content, ' ', $characterLimit);
        if (false === $lastSpace) {
            return $content;
        }

        return substr($content, 0, $lastSpace).'...';
    }
}
