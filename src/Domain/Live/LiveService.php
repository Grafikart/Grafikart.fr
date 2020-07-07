<?php

namespace App\Domain\Live;

use App\Core\OptionInterface;
use Google_Service_YouTube_Video;
use Symfony\Component\Serializer\SerializerInterface;

class LiveService
{

    const OPTION_KEY = "live_id";
    private OptionInterface $option;
    private \Google_Service_YouTube $youTube;
    private SerializerInterface $serializer;

    public function __construct(
        OptionInterface $option,
        \Google_Service_YouTube $youTube,
    SerializerInterface $serializer
    )
    {
        $this->option = $option;
        $this->youTube = $youTube;
        $this->serializer = $serializer;
    }

    public function getCurrentLive (): ?Live
    {
        $option = $this->option->get(self::OPTION_KEY);
        return $option === null ? null : $this->serializer->deserialize($option, Live::class, 'json');
    }

    public function setCurrentLive(?string $live): void
    {
        if ($live === null) {
            $this->option->delete(self::OPTION_KEY);
            return;
        }
        $response = $this->youTube->videos->listVideos('snippet,liveStreamingDetails', [
            'id' => $live
        ]);
        /** @var Google_Service_YouTube_Video[] $videos */
        $videos = $response->getItems();
        $video = $videos[0];
        $publishedAt = new \DateTime($video->getLiveStreamingDetails()->getScheduledStartTime());
        $live = (new Live())
            ->setYoutubeId($video->getId())
            ->setName($video->getSnippet()->getTitle())
            ->setCreatedAt($publishedAt)
            ->setUpdatedAt($publishedAt);
        $this->option->set(self::OPTION_KEY, $this->serializer->serialize($live, 'json'));
    }

}
