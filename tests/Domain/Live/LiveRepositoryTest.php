<?php

namespace App\Tests\Domain\Live;

use App\Domain\Live\Live;
use App\Domain\Live\LiveRepository;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class LiveRepositoryTest extends KernelTestCase
{

    use FixturesTrait;

    private LiveRepository $repository;

    public function setUp(): void
    {
        static::bootKernel();
        $this->repository = self::$container->get(LiveRepository::class);
        parent::setUp();
    }

    public function testLastCreationDate(): void
    {
        /** @var array<Live, string> $lives */
        $lives = $this->loadFixtureFiles([__DIR__ . '/live.yaml']);
        $date = $this->repository->lastCreationDate();
        $this->assertEquals($lives['latest-live']->getCreatedAt(), $date);
    }

}
