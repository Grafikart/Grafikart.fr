<?php

namespace App\Tests\Domain\Course\Service;

use App\Domain\Course\Entity\Course;
use App\Domain\Course\DTO\ContentTechnologyDTO;
use App\Domain\Course\Entity\Technology;
use App\Domain\Course\Entity\TechnologyUsage;
use App\Domain\Course\Repository\TechnologyRepository;
use App\Domain\Course\Service\TechnologySyncService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class TechnologySyncServiceTest extends TestCase
{
    private TechnologySyncService $service;
    private EntityManagerInterface $em;

    protected function setUp(): void
    {
        parent::setUp();
        $this->em = $this->createMock(EntityManagerInterface::class);
        $this->service = new TechnologySyncService($this->em);
        $this->em->expects($this->any())
            ->method('getReference')
            ->willReturnCallback(function (string $class, int $id) {
                $entity = new $class();

                $ref = new \ReflectionProperty($class, 'id');
                $ref->setAccessible(true);
                $ref->setValue($entity, $id);

                return $entity;
            });
    }

    public function testSyncWithEmptyContentAndOneTechnology(): void
    {
        $course = new Course();
        $data = [
            new ContentTechnologyDTO(
                id: 1,
            )
        ];
        $this->service->sync($course, $data);
        $this->assertCount(1, $course->getTechnologyUsages());
    }

    public function testSyncRemovesOldTechnologies(): void
    {
        $course = new Course();
        $t1 = (new TechnologyUsage())->setTechnology($this->em->getReference(Technology::class, 1))->setContent($course);
        $t2 = (new TechnologyUsage())->setTechnology($this->em->getReference(Technology::class, 2))->setContent($course);
        $course->addTechnologyUsage($t1);
        $course->addTechnologyUsage($t2);

        $data = [
            new ContentTechnologyDTO(
                id: 2,
            ),
            new ContentTechnologyDTO(
                id: 3,
            ),
        ];
        $this->service->sync($course, $data);
        $this->assertCount(2, $course->getTechnologyUsages());
        $this->assertEquals(2, $course->getTechnologyUsages()->first()->getTechnology()->getId());
    }
}
