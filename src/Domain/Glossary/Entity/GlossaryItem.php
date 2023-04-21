<?php

namespace App\Domain\Glossary\Entity;

use App\Domain\Glossary\Repository\GlossaryItemRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GlossaryItemRepository::class)]
class GlossaryItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $content = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'synonyms')]
    private ?self $synonym = null;

    #[ORM\OneToMany(mappedBy: 'synonym', targetEntity: self::class)]
    private Collection $synonyms;

    public function __construct()
    {
        $this->synonyms = new ArrayCollection();
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

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable|\DateTime $createdAt): self
    {
        $this->createdAt = $createdAt instanceof \DateTime ? \DateTimeImmutable::createFromMutable($createdAt) : $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable|\DateTime $updatedAt): self
    {
        $this->updatedAt = $updatedAt instanceof \DateTime ? \DateTimeImmutable::createFromMutable($updatedAt) : $updatedAt;

        return $this;
    }

    public function getSynonym(): ?self
    {
        return $this->synonym;
    }

    public function setSynonym(?self $synonym): self
    {
        $this->synonym = $synonym;

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getSynonyms(): Collection
    {
        return $this->synonyms;
    }

    public function addSynonym(self $synonim): self
    {
        if (!$this->synonyms->contains($synonim)) {
            $this->synonyms->add($synonim);
            $synonim->setSynonym($this);
        }

        return $this;
    }

    public function removeSynonym(self $synonim): self
    {
        if ($this->synonyms->removeElement($synonim)) {
            // set the owning side to null (unless already changed)
            if ($synonim->getSynonym() === $this) {
                $synonim->setSynonym(null);
            }
        }

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }
}
