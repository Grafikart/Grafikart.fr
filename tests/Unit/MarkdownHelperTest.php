<?php

use App\Helpers\MarkdownHelper;

describe('markdown math', function () {
    it('renders inline math formulas', function () {
        $html = MarkdownHelper::html('La formule $E=\\alpha _{m}m^{2}Z$ fonctionne.');

        expect($html)->toBe('<p>La formule <math-formula display="inline">E=\alpha _{m}m^{2}Z</math-formula> fonctionne.</p>');
    });

    it('renders inline math formulas in list items', function () {
        $html = MarkdownHelper::html('- $m$ est le nombre de buckets');

        expect($html)->toBe('<ul>
<li><math-formula display="inline">m</math-formula> est le nombre de buckets</li>
</ul>');
    });

    it('renders display math formulas', function () {
        $html = MarkdownHelper::html('$$E=\alpha _{m}m^{2}Z$$');

        expect($html)->toBe('<math-formula display="block">E=\alpha _{m}m^{2}Z</math-formula>');
    });

    it('does not render unclosed display math formulas', function () {
        $html = MarkdownHelper::html('$$E=\alpha');

        expect($html)->toBe('<p>$$E=\alpha</p>');
    });

    it('escapes formula content', function () {
        $html = MarkdownHelper::html('$<script>alert(1)</script>$');

        expect($html)->toBe('<p><math-formula display="inline">&lt;script&gt;alert(1)&lt;/script&gt;</math-formula></p>');
    });

    it('does not render math in code spans', function () {
        $html = MarkdownHelper::html('`$E=\alpha$`');

        expect($html)->toBe('<p><code>$E=\alpha$</code></p>');
    });

    it('does not render math in fenced code blocks', function () {
        $html = MarkdownHelper::html("```txt\n\$E=\\alpha\$\n```");

        expect($html)->toBe('<code-block lang="txt">$E=\alpha$</code-block>');
    });
});
