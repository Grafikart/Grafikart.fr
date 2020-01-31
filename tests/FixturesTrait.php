<?php

namespace App\Tests;

trait FixturesTrait
{

    use \Liip\TestFixturesBundle\Test\FixturesTrait {
        loadFixtureFiles as liipLoadFixtureFiles;
    }

    /**
     * @param array<string> $fixtures
     * @return array<string,object>
     */
    public function loadFixtures(array $fixtures): array
    {
        return $this->liipLoadFixtureFiles(array_map(fn($fixture) => __DIR__ . '/fixtures/' . $fixture . '.yaml', $fixtures));
    }

}
