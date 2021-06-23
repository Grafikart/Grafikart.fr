<?php

namespace App\Tests\Domain\Podcast\EventListener;

use App\Domain\Podcast\Entity\Podcast;
use App\Tests\FixturesTrait;
use App\Tests\KernelTestCase;
use Doctrine\Common\Annotations\Annotation\IgnoreAnnotation;

/**
 * @IgnoreAnnotation("dataProvider")
 */
class PodcastDurationUpdaterTest extends KernelTestCase
{
    use FixturesTrait;

    public function getData(): iterable
    {
        yield ['video-10.mp4', 10];
        yield ['video-100.mp4', 100];
    }

    /**
     * @dataProvider getData
     */
    public function testUpdateDuration(string $mp3, int $expectedDuration): void
    {
        /** @var Podcast $podcast */
        ['podcast1' => $podcast] = $this->loadFixtures(['podcasts']);
        $podcast->setMp3($mp3);
        $this->em->flush();
        $this->assertEquals($expectedDuration, $podcast->getDuration());
    }
}
