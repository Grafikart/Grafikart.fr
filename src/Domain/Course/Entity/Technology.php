<?php

namespace App\Domain\Course\Entity;

use Cocur\Slugify\Slugify;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity
 * @Vich\Uploadable()
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
     * @Vich\UploadableField(fileNameProperty="image", mapping="icons")
     */
    private ?File $imageFile = null;

    /**
     * @ORM\OneToMany(targetEntity="App\Domain\Course\Entity\TechnologyUsage", mappedBy="technology", orphanRemoval=true)
     *
     * @var Collection<int, TechnologyUsage>
     */
    private Collection $usages;

    private bool $secondary = false;

    private ?string $version = null;

    /**
     * @ORM\Column(type="datetime", options={"default": "CURRENT_TIMESTAMP"})
     */
    private ?\DateTimeInterface $updatedAt = null;

    /**
     * @ORM\ManyToMany(targetEntity="App\Domain\Course\Entity\Technology", inversedBy="requiredBy")
     * @ORM\JoinTable(name="technology_requirement")
     *
     * @var Collection<int, Technology>
     */
    private Collection $requirements;

    /**
     * @ORM\ManyToMany(targetEntity="App\Domain\Course\Entity\Technology", mappedBy="requirements")
     *
     * @var Collection<int, Technology>
     */
    private Collection $requiredBy;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private ?string $type = null;

    public function __construct()
    {
        $this->usages = new ArrayCollection();
        $this->requirements = new ArrayCollection();
        $this->requiredBy = new ArrayCollection();
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
        if (null === $this->slug && $this->name) {
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

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function setImageFile(?File $imageFile): Technology
    {
        $this->imageFile = $imageFile;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): Technology
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return Collection<int, Technology>
     */
    public function getRequirements(): Collection
    {
        return $this->requirements;
    }

    public function addRequirement(self $requirement): self
    {
        if (!$this->requirements->contains($requirement)) {
            $this->requirements[] = $requirement;
        }

        return $this;
    }

    public function removeRequirement(self $requirement): self
    {
        if ($this->requirements->contains($requirement)) {
            $this->requirements->removeElement($requirement);
        }

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return Collection<int, Technology>
     */
    public function getRequiredBy()
    {
        return $this->requiredBy;
    }
}
