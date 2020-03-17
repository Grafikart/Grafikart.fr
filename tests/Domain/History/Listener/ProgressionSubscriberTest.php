<?php

namespace App\Tests\Domain\History\Listener;

use App\Domain\Auth\User;
use App\Domain\Course\Entity\Course;
use App\Domain\History\Entity\Progress;
use App\Domain\History\Event\ProgressEvent;
use App\Domain\History\Listener\ProgressionSubscriber;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class ProgressionSubscriberTest extends TestCase
{

    private function getEvent(int $progress = 60): ProgressEvent
    {
        $content = (new Course())->setId(100);
        $user = (new User())->setId(10);
        return new ProgressEvent($content, $user, $progress);
    }

    private function getSubscriber(ProgressEvent $event, ?Progress $progress, $persistCalls = 0): ProgressionSubscriber
    {
        $em = $this->getMockBuilder(EntityManagerInterface::class)->getMock();
        $repository = $this->getMockBuilder(ServiceEntityRepository::class)->disableOriginalConstructor()->getMock();
        $repository->expects($this->once())->method('findOneBy')->with(
            $this->equalTo([
                'content' => $event->getContent(),
                'author'  => $event->getUser()
            ])
        )->willReturn($progress);
        $em->expects($this->exactly($persistCalls))->method('persist');
        $em->expects($this->any())->method('getRepository')->with($this->equalTo(Progress::class))->willReturn($repository);
        return new ProgressionSubscriber($em);
    }

    public function testInsertIfNoPreviousData()
    {
        $event = $this->getEvent();
        $this->getSubscriber($event, null, 1)->onProgress($event);
    }

    public function testUpdateIfPreviousDataExists()
    {
        $event = $this->getEvent();
        $progress = (new Progress())
            ->setContent($event->getContent())
            ->setAuthor($event->getUser())
            ->setCreatedAt(new \DateTime('@1231203'))
            ->setUpdatedAt(new \DateTime('@1231203'));
        $this->getSubscriber($event, $progress, 0)->onProgress($event);
        $this->assertEquals(60, $progress->getPercent());
        $this->assertNotEquals($progress->getCreatedAt(), $progress->getUpdatedAt());
    }

}
