<?php

namespace App\Tests\Infrastructure\Search\Normalizer;

use App\Infrastructure\Search\Normalizer\MarkdownTransformer;
use PHPUnit\Framework\TestCase;

class MarkdownTransformerTest extends TestCase
{
    public function testCleanCode()
    {
        $this->assertEquals('Je fais un test pour voir', MarkdownTransformer::toText('Je **fais** un test pour _voir_'));
    }

    public function testDoesNotEscapeOneLineCode()
    {
        $this->assertEquals('Je fais un test pour voir', MarkdownTransformer::toText('Je **fais** un `test` pour _voir_'));
    }

    public function testRemoveCodeBlocks()
    {
        $this->assertEquals(
            "Je fais un test pour voir.\n\nEt ici on voit du code",
            MarkdownTransformer::toText(<<<MARKDOWN
Je fais un test pour voir.

```js
$('jquery').is.cool()
```

Et ici on voit du code
MARKDOWN)
        );
    }
}
