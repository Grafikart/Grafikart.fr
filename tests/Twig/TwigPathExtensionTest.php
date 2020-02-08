<?php

namespace App\Tests\Twig;

use App\Twig\TwigPathExtension;
use PHPUnit\Framework\TestCase;

class TwigPathExtensionTest extends TestCase
{

    public function testUploadPath(): void
    {
        $extension = new TwigPathExtension();
        $this->assertEquals('/uploads/icon/demo.svg', $extension->uploadsPath('icon/demo.svg'));
        $this->assertEquals('/uploads/icon/demo.svg', $extension->uploadsPath('/icon/demo.svg'));
    }

}
