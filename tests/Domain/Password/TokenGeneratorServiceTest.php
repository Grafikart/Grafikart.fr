<?php

namespace App\Tests\Domain\Password;

use App\Domain\Password\TokenGeneratorService;
use PHPUnit\Framework\TestCase;

class TokenGeneratorServiceTest extends TestCase
{
    public function testGenerateToken(): void
    {
        $service = new TokenGeneratorService();
        for ($i = 1; $i <= 20; ++$i) {
            $this->assertEquals($i, \mb_strlen($service->generate($i)));
        }
    }
}
