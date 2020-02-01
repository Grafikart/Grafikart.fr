<?php

namespace App\Domain\Live;

use Doctrine\ORM\Mapping as ORM;
use Google_Service_YouTube_Thumbnail;

/**
 * @ORM\Entity(repositoryClass="App\Domain\Live\LiveRepository")
 */
class Live
{

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $name;

    /**
     * @ORM\Column(type="text")
     */
    private string $description;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private string $youtube_id;

    /**
     * @ORM\Column(type="integer")
     */
    private int $duration = 0;

    /**
     * @ORM\Column(type="datetime")
     */
    private \DateTimeInterface $created_at;

    /**
     * @ORM\Column(type="datetime")
     */
    private \DateTimeInterface $updated_at;

    private string $youtube_thumbnail;

    public static function fromPlayListItem(\Google_Service_YouTube_PlaylistItem $item): self
    {
        $publishedAt = new \DateTime($item->getSnippet()->getPublishedAt());
        /** @var Google_Service_YouTube_Thumbnail|null $thumbnail */
        $thumbnail = $item->getSnippet()->getThumbnails()->getMaxres();
        return (new self())
            ->setCreatedAt($publishedAt)
            ->setYoutubeId($item->getSnippet()->getResourceId()->getVideoId())
            ->setName($item->getSnippet()->getTitle())
            ->setDescription($item->getSnippet()->getDescription())
            ->setYoutubeThumbnail($thumbnail ? $thumbnail->getUrl() : null)
            ->setDuration((int)$item->getContentDetails()->getEndAt())
            ->setUpdatedAt($publishedAt);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getYoutubeId(): ?string
    {
        return $this->youtube_id;
    }

    public function setYoutubeId(string $youtube): self
    {
        $this->youtube_id = $youtube;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(\DateTimeInterface $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    /**
     * @return string
     */
    public function getYoutubeThumbnail(): string
    {
        return $this->youtube_thumbnail;
    }

    /**
     * @param string $youtube_thumbnail
     */
    public function setYoutubeThumbnail(?string $youtube_thumbnail): self
    {
        $this->youtube_thumbnail = $youtube_thumbnail ?? "https://i.ytimg.com/vi/{$this->getYoutubeId()}/maxresdefault.jpg";

        return $this;
    }

    public function getThumbnailPath(): string
    {
        return "lives/{$this->getYoutubeId()}.jpg";
    }

    public function getDuration(): int
    {
        return $this->duration;
    }

    public function setDuration(int $duration): self
    {
        $this->duration = $duration;

        return $this;
    }


}
