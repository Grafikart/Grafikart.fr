<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    /**
     * @inheritDoc
     */
    public function load(\Doctrine\Persistence\ObjectManager $manager): void
    {
        $manager->flush();
    }
}
