<?php

namespace App\Tests\Domain\History\Listener;

use App\Domain\Auth\User;
use App\Domain\Course\Entity\Course;
use App\Domain\Course\Entity\Formation;
use App\Domain\History\Entity\Progress;
use App\Domain\History\Event\ProgressEvent;
use App\Domain\History\Listener\ProgressionSubscriber;
use App\Domain\History\Repository\ProgressRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ProgressionSubscriberTest extends TestCase
{
    /**
     * @var MockObject|ProgressRepository
     */
    private MockObject $repository;

    /**
     * @var MockObject|EntityManagerInterface
     */
    private MockObject $em;

    /**
     * @var MockObject|EventDispatcherInterface
     */
    private MockObject $dispatcher;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->getMockBuilder(ServiceEntityRepository::class)->disableOriginalConstructor()->getMock();
        $this->em = $this->getMockBuilder(EntityManagerInterface::class)->getMock();
        $this->em->expects($this->any())->method('getRepository')->with($this->equalTo(Progress::class))->willReturn($this->repository);
        $this->dispatcher = $this->getMockBuilder(EventDispatcherInterface::class)->getMock();
    }

    private function getEvent(float $progress = 0.6): ProgressEvent
    {
        $content = (new Course())->setId(100);
        $user = (new User())->setId(10);

        return new ProgressEvent($content, $user, $progress);
    }

    private function getSubscriber(ProgressEvent $event, ?Progress $progress = null, $persistCalls = 0): ProgressionSubscriber
    {
        $this->repository->expects($this->once())->method('findOneBy')->with(
            $this->equalTo([
                'content' => $event->getContent(),
                'author' => $event->getUser(),
            ])
        )->willReturn($progress);
        $this->em->expects($this->exactly($persistCalls))->method('persist');

        return new ProgressionSubscriber($this->em, $this->dispatcher);
    }

    public function testInsertIfNoPreviousData()
    {
        $event = $this->getEvent();
        $this->dispatcher->expects($this->never())->method('dispatch');
        $this->getSubscriber($event, null, 1)->onProgress($event);
    }

    public function testUpdateIfPreviousDataExists()
    {
        $event = $this->getEvent();
        $progress = (new Progress())
            ->setContent($event->getContent())
            ->setAuthor($event->getUser())
            ->setCreatedAt(new \DateTimeImmutable('@1231203'))
            ->setUpdatedAt(new \DateTimeImmutable('@1231203'));
        $this->getSubscriber($event, $progress, 0)->onProgress($event);
        $this->assertEquals(0.6, $progress->getRatio());
        $this->assertNotEquals($progress->getCreatedAt(), $progress->getUpdatedAt());
    }

    public function dataFormationProgressionUpdater(): iterable
    {
        yield [1, .1];
        yield [3, .3];
    }

    /**
     * @dataProvider dataFormationProgressionUpdater
     */
    public function testUpdateProgressionForFormation(int $completedCourses, float $expectedProgress): void
    {
        // On crée une formation avec 10 cours
        $formation = new Formation();
        $courses = [];
        $ids = [];
        for ($i = 0; $i < 10; ++$i) {
            $course = (new Course())->setId($i);
            $formation->addCourse($course);
            $courses[] = $course;
            if (1 !== $i) {
                $ids[] = $i;
            }
        }

        // On crée l'event
        $user = (new User())->setId(10);
        $event = new ProgressEvent($courses[1], $user, 1);

        $this->repository->expects($this->once())->method('count')->with(
            $this->equalTo(['content' => $ids, 'progress' => 1000, 'author' => 10])
        )->willReturn($completedCourses - 1);

        $expected = new ProgressEvent($formation, $event->getUser(), $expectedProgress);

        $this->dispatcher->expects($this->once())->method('dispatch')->with(
            $this->equalTo($expected)
        );

        $this->getSubscriber($event, null, 1)->onProgress($event);
    }
}
