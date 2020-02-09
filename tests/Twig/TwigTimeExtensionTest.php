<?php

namespace App\Tests\Twig;

use App\Twig\TwigTimeExtension;
use PHPUnit\Framework\TestCase;

class TwigTimeExtensionTest extends TestCase
{

    private TwigTimeExtension $extension;

    public function setUp(): void
    {
        $this->extension = new TwigTimeExtension();
    }

    public function testDurationWithMinutes(): void
    {
        $this->assertEquals('30 min', $this->extension->duration(30 * 60));
        $this->assertEquals('59 min', $this->extension->duration(58 * 60 + 55));
    }

    public function testDurationWithHours(): void
    {
        $this->assertEquals('1h30', $this->extension->duration(90 * 60));
        $this->assertEquals('1h31', $this->extension->duration(90 * 60 + 55));
    }


}
