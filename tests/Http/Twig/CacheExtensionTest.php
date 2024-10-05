<?php

namespace App\Tests\Http\Twig;

use App\Http\Twig\TwigCacheExtension;
use PHPUnit\Framework\TestCase;
use Psr\Cache\CacheItemPoolInterface;
use Spatie\Snapshots\MatchesSnapshots;
use Twig\Environment;
use Twig\Loader\ArrayLoader;
use Twig\Source;

class CacheExtensionTest extends TestCase
{

    use MatchesSnapshots;

    public function testCacheCompilation()
    {
        $twig = new Environment(new ArrayLoader(['index.twig' => '']));
        $twig->addExtension(new TwigCacheExtension(
           $this->getMockBuilder(CacheItemPoolInterface::class)->getMock(),
            true,
        ));
        $source = new Source(<<<TWIG
{% cache [demo] %}
    {{ firstname}} {{ lastname }}
{% endcache %}
TWIG, 'index.twig');
        $this->assertMatchesTextSnapshot($twig->compileSource($source));
    }

}
