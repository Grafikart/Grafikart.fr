<?php

namespace App\Tests\Core\Twig;

use App\Core\Twig\TwigTimeExtension;
use DateTime;
use PHPUnit\Framework\TestCase;

class TwigTimeExtensionTest extends TestCase
{
    private TwigTimeExtension $extension;

    public function setUp(): void
    {
        $this->extension = new TwigTimeExtension();
    }

    public function dataTestDuration(): iterable
    {
        yield ['30 min', 30 * 60];
        yield ['59 min', 58 * 60 + 55];
        yield ['1h30', 90 * 60];
        yield ['1h31', 90 * 60 + 55];
    }

    /**
     * @dataProvider dataTestDuration
     */
    public function testDuration(string $expected, int $time): void
    {
        $this->assertEquals($expected, $this->extension->duration($time));
    }

    public function dataTestAgo()
    {
        return [[1231323232], [598498490], [779798765406]];
    }

    /**
     * @dataProvider dataTestAgo
     */
    public function testAgo(int $time): void
    {
        $date = new DateTime('@'.$time);
        $this->assertEquals("<time-ago time=\"$time\"></time-ago>", $this->extension->ago($date));
    }

    public function dataTestShortDuration(): iterable
    {
        yield ['30:00', 30 * 60];
        yield ['58:55', 58 * 60 + 55];
        yield ['01:30:00', 90 * 60];
        yield ['01:30:55', 90 * 60 + 55];
    }

    /**
     * @dataProvider dataTestShortDuration
     */
    public function testShortDuration(string $expected, int $time): void
    {
        $this->assertEquals($expected, $this->extension->shortDuration($time));
    }
}
