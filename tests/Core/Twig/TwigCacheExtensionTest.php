<?php

namespace App\Tests\Core\Twig;

use App\Core\Twig\TwigCacheExtension;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\Cache\CacheItem;

class TwigCacheExtensionTest extends TestCase
{

    /**
     * @var MockObject|AdapterInterface cache
     */
    private $cache;

    private TwigCacheExtension $extension;

    public function setUp(): void
    {
        /** @var MockObject|AdapterInterface cache */
        $this->cache = $this->getMockBuilder(AdapterInterface::class)->getMock();
        $this->extension = new TwigCacheExtension($this->cache);
    }


    public function cacheKeys(): iterable
    {
        yield ['salut', 'salut'];
        yield ['salut-aurevoir', ['salut', 'aurevoir']];

        $fake = new FakeClass();
        yield [
            $fake->getId() . 'FakeClass' . $fake->getUpdatedAt()->getTimestamp(),
            $fake
        ];
        yield [
            'card-' . $fake->getId() . 'FakeClass' . $fake->getUpdatedAt()->getTimestamp(),
            ['card', $fake]
        ];
    }

    /**
     * @dataProvider cacheKeys
     */
    public function testCacheKeyGeneration($expected, $value): void
    {
        $this->assertEquals($expected, $this->extension->getCacheKey($value));
    }

    public function testCacheKeyWithBadValues(): void
    {
        $this->expectException(\Exception::class);
        $this->extension->getCacheKey([]);
    }

    public function testSetCacheValue(): void
    {
        $item = new CacheItem();
        $this->cache->expects($this->any())->method('getItem')
            ->with('demo')
            ->willReturn($item);
        $this->extension->setCacheValue('demo', 'Salut');
        $this->assertEquals('Salut', $item->get());
    }

    public function testGetCacheValue(): void
    {
        $item = new CacheItem();
        $item->set('hello');
        $this->cache->expects($this->any())->method('getItem')
            ->with('demo')
            ->willReturn($item);
        $value = $this->extension->getCacheValue('demo');
        $this->assertEquals($item->get(), $value);
    }

    public function testGetCacheValueWithoutValue(): void
    {
        $item = new CacheItem();
        $this->cache->expects($this->any())->method('getItem')
            ->with('demo')
            ->willReturn($item);
        $value = $this->extension->getCacheValue('demo');
        $this->assertEquals(null, $value);
    }




}
