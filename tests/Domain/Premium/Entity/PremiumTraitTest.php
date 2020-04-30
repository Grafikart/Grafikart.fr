<?php

namespace App\Tests\Domain\Premium\Entity;

use PHPUnit\Framework\TestCase;

class PremiumTraitTest extends TestCase
{

    public function testNotPremium () {
        $user = new PremiumTraitUser();
        $this->assertFalse($user->isPremium());
    }

    public function testPremiumExpired () {
        $user = new PremiumTraitUser();
        $user->setPremiumEnd(new \DateTime('-1 year'));
        $this->assertFalse($user->isPremium());
    }

    public function testPremium () {
        $user = new PremiumTraitUser();
        $user->setPremiumEnd(new \DateTime('+1 year'));
        $this->assertTrue($user->isPremium());
    }

}
