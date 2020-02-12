<?php

namespace App\Domain\Course\Entity;

use App\Domain\Application\Entity\Content;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Formation extends Content
{

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $short;

    /**
     * @ORM\Column(type="string")
     */
    private ?string $image;

    /**
     * @ORM\Column(type="json")
     * @var array<string>
     */
    private array $chapters = [];

    /**
     * @ORM\Column(type="integer")
     */
    private int $duration = 0;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $youtube_playlist = '';

    /**
     * @ORM\OneToMany(targetEntity="App\Domain\Course\Entity\Course", mappedBy="formation")
     * @var Collection<int, Course>
     */
    private $courses;

    public function __construct()
    {
        $this->courses = new ArrayCollection();
    }

    public function getShort(): ?string
    {
        return $this->short;
    }

    public function setShort(?string $short): self
    {
        $this->short = $short;
        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;
        return $this;
    }

    /**
     * @return array<string>
     */
    public function getChapters(): array
    {
        return $this->chapters;
    }

    /**
     * @param array<string> $chapters
     */
    public function setChapters(array $chapters): self
    {
        $this->chapters = $chapters;

        return $this;
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

    public function getYoutubePlaylist(): string
    {
        return $this->youtube_playlist;
    }

    public function setYoutubePlaylist(string $youtube_playlist): self
    {
        $this->youtube_playlist = $youtube_playlist;

        return $this;
    }

    /**
     * @return Collection<int, Course>
     */
    public function getCourses(): Collection
    {
        return $this->courses;
    }

    public function addCourse(Course $course): self
    {
        if (!$this->courses->contains($course)) {
            $this->courses[] = $course;
            $course->setFormation($this);
        }

        return $this;
    }

    public function removeCourse(Course $course): self
    {
        if ($this->courses->contains($course)) {
            $this->courses->removeElement($course);
            // set the owning side to null (unless already changed)
            if ($course->getFormation() === $this) {
                $course->setFormation(null);
            }
        }

        return $this;
    }
}
