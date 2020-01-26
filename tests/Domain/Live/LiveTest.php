<?php

namespace App\Tests\Domain\Live;

use App\Domain\Live\Live;
use App\Tests\Helper\GoogleTestHelper;
use PHPUnit\Framework\TestCase;

class LiveTest extends TestCase
{

    public function testGetThumbnailPath(): void
    {
        $live = new Live();
        $live->setYoutubeId('video1');
        $this->assertEquals('lives/video1.jpg', $live->getThumbnailPath());
    }

    public function testLiveFactory(): void
    {
        $item = GoogleTestHelper::fakeYoutubePlaylistItem([
            'description' => 'How are you',
            'title'       => 'Hello world',
            'id'          => '123123',
            'date'        => '+ 1 year'
        ]);
        $live = Live::fromPlayListItem($item);
        $this->assertEquals('Hello world', $live->getName());
        $this->assertEquals('How are you', $live->getDescription());
        $this->assertGreaterThan(new \DateTime(), $live->getCreatedAt());
        $this->assertGreaterThan(new \DateTime(), $live->getUpdatedAt());
        $this->assertEquals('123123', $live->getYoutubeId());
    }

}
