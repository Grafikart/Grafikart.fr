<?php

namespace App\Domain\Course\Entity;

use App\Domain\Application\Entity\Content;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Course extends Content
{

    /**
     * @ORM\Column(type="smallint", options={"default": 0})
     */
    private int $duration = 0;

    /**
     * @ORM\Column(type="string", length=12, nullable=true)
     */
    private ?string $youtubeId;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $videoPath;

    /**
     * @ORM\Column(type="boolean", options={"default": 0})
     */
    private bool $source = false;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $demo;

    /**
     * @ORM\Column(type="boolean", options={"default": 0})
     */
    private bool $premium = false;

    /**
     * @ORM\ManyToOne(targetEntity="App\Domain\Course\Entity\Course")
     */
    private ?Course $deprecatedBy;

    /**
     * @ORM\ManyToOne(targetEntity="App\Domain\Course\Entity\Formation", inversedBy="courses")
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    private ?Formation $formation;

    public function getDuration(): int
    {
        return $this->duration;
    }

    public function setDuration(int $duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    public function getYoutubeId(): ?string
    {
        return $this->youtubeId;
    }

    public function setYoutubeId(?string $youtubeId): self
    {
        $this->youtubeId = $youtubeId;

        return $this;
    }

    public function getVideoPath(): ?string
    {
        return $this->videoPath;
    }

    public function setVideoPath(?string $videoPath): self
    {
        $this->videoPath = $videoPath;

        return $this;
    }

    public function getSource(): bool
    {
        return $this->source;
    }

    public function setSource(bool $source): self
    {
        $this->source = $source;

        return $this;
    }

    public function getDemo(): ?string
    {
        return $this->demo;
    }

    public function setDemo(?string $demo): self
    {
        $this->demo = $demo;

        return $this;
    }

    public function getPremium(): bool
    {
        return $this->premium;
    }

    public function setPremium(bool $premium): self
    {
        $this->premium = $premium;

        return $this;
    }

    public function getDeprecatedBy(): ?self
    {
        return $this->deprecatedBy;
    }

    public function setDeprecatedBy(?self $deprecatedBy): self
    {
        $this->deprecatedBy = $deprecatedBy;

        return $this;
    }

    public function getFormation(): ?Formation
    {
        return $this->formation;
    }

    public function setFormation(?Formation $formation): self
    {
        $this->formation = $formation;

        return $this;
    }

}
