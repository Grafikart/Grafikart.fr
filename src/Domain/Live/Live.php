<?php

namespace App\Domain\Live;

use App\Core\UploaderBundle\RemoteFile;
use Doctrine\ORM\Mapping as ORM;
use Google_Service_YouTube_Thumbnail;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass="App\Domain\Live\LiveRepository")
 * @Vich\Uploadable()
 */
class Live
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
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
    private string $description = '';

    /**
     * @ORM\Column(type="string", length=20)
     */
    private string $youtubeId;

    /**
     * @ORM\Column(type="integer")
     */
    private int $duration = 0;

    /**
     * @ORM\Column(type="datetime")
     */
    private \DateTimeInterface $createdAt;

    /**
     * @ORM\Column(type="datetime")
     */
    private \DateTimeInterface $updated_at;

    /**
     * @Vich\UploadableField(mapping="lives", fileNameProperty="imageName")
     */
    private ?File $image = null;

    /**
     * @ORM\Column(type="string", name="image", nullable=true)
     */
    private ?string $imageName = null;

    public static function fromYoutubeVideo(\Google_Service_YouTube_Video $video): self
    {
        $publishedAt = new \DateTime($video->getSnippet()->getPublishedAt());
        /** @var Google_Service_YouTube_Thumbnail|null $thumbnail */
        $thumbnail = $video->getSnippet()->getThumbnails()->getMaxres();
        /** @var Google_Service_YouTube_Thumbnail|null $thumbnail */
        $thumbnail = $thumbnail ?: $video->getSnippet()->getThumbnails()->getHigh();

        return (new self())
            ->setCreatedAt($publishedAt)
            ->setYoutubeId($video->getId())
            ->setName($video->getSnippet()->getTitle())
            ->setDescription($video->getSnippet()->getDescription())
            ->setImage($thumbnail ? new RemoteFile($thumbnail->getUrl()) : null)
            ->setDuration(new \DateInterval($video->getContentDetails()->getDuration()))
            ->setUpdatedAt($publishedAt);
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
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

    public function getYoutubeUrl(): ?string
    {
        return 'https://youtu.be/'.$this->youtubeId;
    }

    public function getYoutubeId(): ?string
    {
        return $this->youtubeId;
    }

    public function setYoutubeId(string $youtube): self
    {
        $this->youtubeId = $youtube;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

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

    public function getDuration(): int
    {
        return $this->duration;
    }

    /**
     * @param int|\DateInterval $duration
     *
     * @return $this
     */
    public function setDuration($duration): self
    {
        if ($duration instanceof \DateInterval) {
            $this->duration = 3600 * $duration->h + 60 * $duration->i + $duration->s;
        } else {
            $this->duration = $duration;
        }

        return $this;
    }

    public function getImage(): ?File
    {
        return $this->image;
    }

    public function setImage(?File $image): Live
    {
        $this->image = $image;

        return $this;
    }

    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    public function setImageName(?string $imageName): Live
    {
        $this->imageName = $imageName;

        return $this;
    }
}
