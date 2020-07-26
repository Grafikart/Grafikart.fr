<?php

namespace App\Tests\Infrastructure\Payment;

use App\Domain\Auth\User;
use App\Infrastructure\Payment\VatService;
use PHPUnit\Framework\TestCase;

class VatServiceTest extends TestCase
{
    public function dataProvider(): iterable
    {
        yield ['CM', 10.0, 10.0];
        yield ['FR', 10.0, 12.0];
        yield ['FR', 18.99, 18.99 + 3.79];
    }

    /**
     * @dataProvider dataProvider
     */
    public function testWithoutTax(string $countryCode, float $price, float $expectedPrice): void
    {
        $user = (new User())->setCountry($countryCode);
        $vatService = new VatService();
        $this->assertSame($expectedPrice, $vatService->vatPrice($price, $user));
    }
}
