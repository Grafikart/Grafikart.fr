<?php

namespace App\Tests\Domain\Live;

use App\Domain\Live\Live;
use App\Domain\Live\LiveCreatedEvent;
use App\Domain\Live\ThumbnailPersister;
use App\Tests\EventSubscriberTest;
use League\Flysystem\FilesystemInterface;
use PHPUnit\Framework\MockObject\MockObject;

class ThumbnailPersisterTest extends EventSubscriberTest
{

    public function testPersistFile(): void
    {
        /** @var MockObject|FilesystemInterface $fs */
        $fs = $this->getMockBuilder(FilesystemInterface::class)->getMock();
        $subscriber = new ThumbnailPersister($fs);

        $live = new Live();
        $live->setYoutubeId('video3');
        $live->setYoutubeThumbnail(__FILE__);

        $fs->expects($this->once())->method('has')->willReturn(false);
        $fs->expects($this->once())->method('writeStream')
            ->with($this->equalTo($live->getThumbnailPath()))
            ->willReturn(true);

        $this->dispatch($subscriber, new LiveCreatedEvent($live));
    }

    public function testSubscriberIsListening(): void
    {
        $this->assertSubsribeTo(ThumbnailPersister::class, LiveCreatedEvent::class);
    }

}
