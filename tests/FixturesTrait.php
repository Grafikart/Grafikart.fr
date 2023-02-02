<?php

namespace App\Tests;

use App\Helper\PathHelper;
use Doctrine\ORM\EntityManagerInterface;
use Fidry\AliceDataFixtures\LoaderInterface;

/**
 * @property EntityManagerInterface $em
 */
trait FixturesTrait
{
    /**
     * Charge une série de fixture en base de donnée et ajoute les entités à l'EntityManager.
     *
     * @param array<string> $fixtures
     *
     * @return array<string,object>
     */
    public function loadFixtures(array $fixtures): array
    {
        $fixturePath = $this->getFixturesPath();
        $files = array_map(fn ($fixture) => PathHelper::join($fixturePath, $fixture.'.yaml'), $fixtures);
        /** @var LoaderInterface $loader */
        $loader = $this->getContainer()->get('fidry_alice_data_fixtures.loader.doctrine');

        return $loader->load($files);
    }

    private function getFixturesPath()
    {
        return __DIR__.'/fixtures/';
    }
}
