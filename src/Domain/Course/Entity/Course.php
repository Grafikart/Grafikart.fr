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
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="smallint", options={"default": 0})
     */
    private $duration;

    /**
     * @ORM\Column(type="string", length=12, nullable=true)
     */
    private $youtube_id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $video_path;

    /**
     * @ORM\Column(type="boolean", options={"default": 0})
     */
    private $source;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $demo;

    /**
     * @ORM\Column(type="boolean", options={"default": 0})
     */
    private $premium;

    /**
     * @ORM\ManyToOne(targetEntity="App\Domain\Course\Entity\Course")
     */
    private $deprecated_by;

    /**
     * @ORM\ManyToOne(targetEntity="App\Domain\Course\Entity\Formation", inversedBy="courses")
     */
    private $formation;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDuration(): ?int
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
        return $this->youtube_id;
    }

    public function setYoutubeId(?string $youtube_id): self
    {
        $this->youtube_id = $youtube_id;

        return $this;
    }

    public function getVideoPath(): ?string
    {
        return $this->video_path;
    }

    public function setVideoPath(?string $video_path): self
    {
        $this->video_path = $video_path;

        return $this;
    }

    public function getSource(): ?bool
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

    public function setDemo(string $demo): self
    {
        $this->demo = $demo;

        return $this;
    }

    public function getPremium(): ?bool
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
        return $this->deprecated_by;
    }

    public function setDeprecatedBy(?self $deprecated_by): self
    {
        $this->deprecated_by = $deprecated_by;

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
