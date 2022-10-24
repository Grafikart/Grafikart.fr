<?php

namespace App\Tests\Domain\Password;

use App\Infrastructure\Security\TokenGeneratorService;
use PHPUnit\Framework\TestCase;

class TokenGeneratorServiceTest extends TestCase
{
    public function testGenerateToken(): void
    {
        $service = new TokenGeneratorService();
        for ($i = 2; $i <= 20; ++$i) {
            $this->assertEquals($i, \mb_strlen($service->generate($i)));
        }
    }
}
