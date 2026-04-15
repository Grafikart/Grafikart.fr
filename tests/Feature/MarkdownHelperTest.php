<?php

use App\Helpers\MarkdownHelper;

it('generates an excerpt', function (string $content, string $expected) {
    $excerpt = MarkdownHelper::excerpt($content);
    expect(str_replace("\n", ' ', $excerpt))->toBe($expected);
})->with([
    'standard content' => [
        "Ceci est un test.\nhttps://google.com\nFin du test.",
        'Ceci est un test. Fin du test.',
    ],
    'multiple urls' => [
        "Line 1\nhttp://example.com\nLine 2\nhttps://example.com/test\nLine 3",
        'Line 1 Line 2 Line 3',
    ],
    'excerpt with character limit' => [
        str_repeat('a ', 70),
        str_repeat('a ', 67).'a...',
    ],
    'content with title far away' => [
        "Short content\n\n".str_repeat("a\n", 60).'## Title',
        'Short content '.str_repeat('a ', 59).'a', // 135 chars limit reached before title
    ],
]);
