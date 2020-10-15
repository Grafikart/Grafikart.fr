<?php

namespace App\Infrastructure\Search\Normalizer;

/**
 * Converts markdown into a searchable text removing the markdown syntax.
 */
class MarkdownTransformer
{
    public static function toText(string $content): string
    {
        $html = (new \Parsedown())->setSafeMode(false)->parse($content);
        $html = preg_replace('@<pre>.*?</pre>@si', '', $html);

        return strip_tags($html);
    }
}
