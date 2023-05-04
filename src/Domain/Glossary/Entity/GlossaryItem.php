<?php

namespace App\Domain\Glossary\Entity;

use App\Domain\Glossary\Repository\GlossaryItemRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * @property string[] $synonyms
 */
#[ORM\Entity(repositoryClass: GlossaryItemRepository::class)]
class GlossaryItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 60)]
    private ?string $name = null;

    #[ORM\Column(length: 60)]
    private ?string $slug = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $content = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(type: 'simple_array', nullable: true)]
    private array $synonyms;

    public function __construct()
    {
        $this->synonyms = [];
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

    /**
     * @return string[]
     */
    public function getSynonyms(): array
    {
        return $this->synonyms;
    }

    /**
     * @param string[]|null $synonyms
     */
    public function setSynonyms(?array $synonyms): self
    {
        $this->synonyms = $synonyms ? array_map(fn(string $v) => trim($v), $synonyms) : [];

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
