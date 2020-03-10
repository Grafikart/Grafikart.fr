<?php

namespace App\Domain\Course\Entity;

use Cocur\Slugify\Slugify;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Technology
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $name = null;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $slug = null;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $content = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $image = null;

    /**
     * @ORM\OneToMany(targetEntity="App\Domain\Course\Entity\TechnologyUsage", mappedBy="technology", orphanRemoval=true)
     * @var Collection<int, TechnologyUsage>
     */
    private Collection $usages;

    private bool $secondary = false;

    private ?string $version = null;

    public function __construct()
    {
        $this->usages = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;
        if ($this->slug === null && $this->name) {
            $this->slug = (new Slugify())->slugify($this->name);
        }

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;

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
     * @return Collection<int, TechnologyUsage>
     */
    public function getUsages(): Collection
    {
        return $this->usages;
    }

    public function addUsage(TechnologyUsage $usage): self
    {
        if (!$this->usages->contains($usage)) {
            $this->usages[] = $usage;
            $usage->setTechnology($this);
        }

        return $this;
    }

    public function removeUsage(TechnologyUsage $usage): self
    {
        if ($this->usages->contains($usage)) {
            $this->usages->removeElement($usage);
        }

        return $this;
    }

    public function getVersion(): ?string
    {
        return $this->version;
    }

    public function setVersion(?string $version): self
    {
        $this->version = $version;
        return $this;
    }

    public function __toString()
    {
        return $this->name ?: '';
    }

    public function setSecondary(bool $secondary): self
    {
        $this->secondary = $secondary;
        return $this;
    }

    public function isSecondary(): bool
    {
        return $this->secondary;
    }

}
