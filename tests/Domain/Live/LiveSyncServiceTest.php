<?php

namespace App\Tests\Domain\Live;

use App\Domain\Live\LiveRepository;
use App\Domain\Live\LiveSyncService;
use App\Tests\Helper\GoogleTestHelper;
use Google_Service_YouTube_Resource_PlaylistItems;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class LiveSyncServiceTest extends TestCase
{

    private LiveSyncService $service;

    private MockObject $repository;


    public function setUp(): void
    {
        parent::setUp();
        /** @var MockObject|LiveRepository repository */
        $this->repository = $this->getMockBuilder(LiveRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->service = new LiveSyncService(
            $this->getGooglePlaylistItems(),
            $this->repository,
            'playlistID'
        );
    }

    public function testBuildNewLivesWithoutPreviousLives(): void
    {
        $this->repository->expects($this->once())->method('lastCreationDate')->willReturn(null);
        $lives = $this->service->buildNewLives();
        $this->assertCount(2, $lives);
    }

    public function testBuildNewLivesWithPreviousLives(): void
    {
        $this->repository->expects($this->once())->method('lastCreationDate')->willReturn(new \DateTime('- 2 year'));
        $lives = $this->service->buildNewLives();
        $this->assertCount(1, $lives);
    }

    public function testBuildNewLivesWithNewDate(): void
    {
        $this->repository->expects($this->once())->method('lastCreationDate')->willReturn(new \DateTime());
        $lives = $this->service->buildNewLives();
        $this->assertCount(0, $lives);
    }

    public function testBuildNewLivesRightOrder(): void
    {
        $this->repository->expects($this->once())->method('lastCreationDate')->willReturn(null);
        $lives = $this->service->buildNewLives();
        $this->assertCount(2, $lives);
        $this->assertEquals('video1', $lives[0]->getYoutubeId());
        $this->assertEquals('video2', $lives[1]->getYoutubeId());
    }

    /**
     * @return MockObject|\Google_Service_YouTube_Resource_PlaylistItems
     */
    private function getGooglePlaylistItems(): MockObject
    {
        $playlistItems = $this->getMockBuilder(Google_Service_YouTube_Resource_PlaylistItems::class)
            ->disableOriginalConstructor()
            ->getMock();
        $response = new \Google_Service_YouTube_PlaylistItemListResponse();
        $playlistItems
            ->expects($this->once())
            ->method('listPlaylistItems')
            ->willReturn($response);
        $response->setItems([
            GoogleTestHelper::fakeYoutubePlaylistItem([
                'id' => 'video2',
                'date' => '- 1 year'
            ]),
            GoogleTestHelper::fakeYoutubePlaylistItem([
                'id' => 'video1',
                'date' => '- 5 year'
            ])
        ]);
        return $playlistItems;
    }

}
