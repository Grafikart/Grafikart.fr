<?php

namespace App\Domain\Live;

use App\Core\OptionInterface;
use Google_Service_YouTube_Video;
use Symfony\Component\Serializer\SerializerInterface;

class LiveService
{
    const OPTION_KEY = 'live_id';
    private OptionInterface $option;
    private \Google_Service_YouTube $youTube;
    private SerializerInterface $serializer;

    public function __construct(
        OptionInterface $option,
        \Google_Service_YouTube $youTube,
        SerializerInterface $serializer
    ) {
        $this->option = $option;
        $this->youTube = $youTube;
        $this->serializer = $serializer;
    }

    public function getCurrentLive(): ?Live
    {
        $option = $this->option->get(self::OPTION_KEY);

        return null === $option ? null : $this->serializer->deserialize($option, Live::class, 'json');
    }

    /**
     * Programme un nouveau live.
     */
    public function programLive(\DateTime $date)
    {
        $snippet = new \Google_Service_YouTube_LiveBroadcastSnippet();
        $snippet->setTitle('LiveCoding : Développement du nouveau site');
        $snippet->setDescription("Editeur : PHPStorm https://www.grafikart.fr/formations/phpstorm
Couleur de l'éditeur : Material Theme UI Palenight (https://plugins.jetbrains.com/plugin/8006-material-theme-ui)
OS : Arch Linux avec l'environnement de bureau i3 https://www.grafikart.fr/tutoriels/i3wm-presentation-916");
        $snippet->setScheduledStartTime($date);
        $status = new \Google_Service_YouTube_LiveBroadcastStatus();
        $status->setPrivacyStatus('unlisted');
        $broadcast = new \Google_Service_YouTube_LiveBroadcast();
        $broadcast->setSnippet($snippet);
        $broadcast = $this->youTube->liveBroadcasts->insert('id,snippet,contentDetails,status', $broadcast);
        $this->setCurrentLive($broadcast->getId());
    }

    public function setCurrentLive(?string $live): void
    {
        if (null === $live) {
            $this->option->delete(self::OPTION_KEY);

            return;
        }
        $response = $this->youTube->videos->listVideos('snippet,liveStreamingDetails', [
            'id' => $live,
        ]);
        /** @var Google_Service_YouTube_Video[] $videos */
        $videos = $response->getItems();
        $video = $videos[0];
        $publishedAt = new \DateTime($video->getLiveStreamingDetails()->getScheduledStartTime());
        $live = (new Live())
            ->setId(0)
            ->setYoutubeId($video->getId())
            ->setName($video->getSnippet()->getTitle())
            ->setCreatedAt($publishedAt)
            ->setUpdatedAt($publishedAt);
        $this->option->set(self::OPTION_KEY, $this->serializer->serialize($live, 'json'));
    }
}
