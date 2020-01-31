<?php

namespace App\Tests\Domain\Live;

use App\Domain\Live\Live;
use App\Domain\Live\LiveRepository;
use App\Tests\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class LiveRepositoryTest extends KernelTestCase
{

    use FixturesTrait;

    private LiveRepository $repository;

    public function setUp(): void
    {
        static::bootKernel();
        /** @var LiveRepository $repository */
        $repository = self::$container->get(LiveRepository::class);
        $this->repository = $repository;
        parent::setUp();
    }

    public function testLastCreationDate(): void
    {
        /** @var array<string,Live> $lives */
        $lives = $this->loadFixtures(['lives']);
        $date = $this->repository->lastCreationDate();
        $this->assertEquals($lives['latest-live']->getCreatedAt(), $date);
    }

}
