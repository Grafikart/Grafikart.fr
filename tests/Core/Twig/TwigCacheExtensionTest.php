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
        /* @var MockObject|AdapterInterface cache */
        $this->cache = $this->getMockBuilder(AdapterInterface::class)->getMock();
        $this->extension = new TwigCacheExtension($this->cache);
    }

    public function cacheKeys(): iterable
    {
        yield ['fake_salut', 'salut'];
        yield ['fake_salut_aurevoir', ['salut', 'aurevoir']];

        $fake = new FakeClass();
        yield [
            'fake_'.$fake->getId().'FakeClass'.$fake->getUpdatedAt()->getTimestamp(),
            $fake,
        ];
        yield [
            'fake_card_'.$fake->getId().'FakeClass'.$fake->getUpdatedAt()->getTimestamp(),
            ['card', $fake],
        ];
    }

    /**
     * @dataProvider cacheKeys
     */
    public function testCacheKeyGeneration($expected, $value): void
    {
        $this->assertEquals($expected, $this->extension->getCacheKey('fake.html.twig', $value));
    }

    public function testCacheKeyWithBadValues(): void
    {
        $this->expectException(\Exception::class);
        $this->extension->getCacheKey('fake.html.twig', []);
    }

    public function testSetCacheValue(): void
    {
        $item = new CacheItem();
        $this->cache->expects($this->any())->method('getItem')
            ->with('fake_demo')
            ->willReturn($item);
        $this->extension->setCacheValue('fake.html.twig', 'demo', 'Salut');
        $this->assertEquals('Salut', $item->get());
    }

    public function testGetCacheValue(): void
    {
        $item = new CacheItem();
        $item->set('hello');
        $this->cache->expects($this->any())->method('getItem')
            ->with('fake_demo')
            ->willReturn($item);
        $value = $this->extension->getCacheValue('fake.html.twig', 'demo');
        $this->assertEquals($item->get(), $value);
    }

    public function testGetCacheValueWithoutValue(): void
    {
        $item = new CacheItem();
        $this->cache->expects($this->any())->method('getItem')
            ->with('fake_demo')
            ->willReturn($item);
        $value = $this->extension->getCacheValue('fake.html.twig', 'demo');
        $this->assertEquals(null, $value);
    }
}
