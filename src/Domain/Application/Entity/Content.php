<?php

namespace App\Domain\Application\Entity;

use App\Domain\Attachment\Attachment;
use App\Domain\Auth\User;
use App\Domain\Course\Entity\Technology;
use App\Domain\Course\Entity\TechnologyUsage;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap({
 *     "course" = "App\Domain\Course\Entity\Course",
 *     "formation" = "App\Domain\Course\Entity\Formation",
 *     "post" = "App\Domain\Blog\Post",
 * })
 */
abstract class Content
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
    private string $title = '';

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $slug = '';

    /**
     * @ORM\Column(type="text")
     */
    private string $content = '';

    /**
     * @ORM\Column(type="datetime")
     */
    private ?\DateTimeInterface $createdAt = null;

    /**
     * @ORM\Column(type="datetime")
     */
    private ?\DateTimeInterface $updated_at = null;

    /**
     * @ORM\Column(type="boolean", options={"default": 0})
     */
    private bool $online = false;

    /**
     * @ORM\ManyToOne(targetEntity="App\Domain\Attachment\Attachment", cascade={"persist"})
     * @ORM\JoinColumn(name="attachment_id", referencedColumnName="id")
     */
    private ?Attachment $image = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Domain\Auth\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private ?User $author = null;

    /**
     * @ORM\OneToMany(targetEntity="App\Domain\Course\Entity\TechnologyUsage", mappedBy="content", cascade={"persist"})
     * @var Collection<int, TechnologyUsage> $technologyUsages
     */
    private Collection $technologyUsages;

    public function __construct()
    {
        $this->technologyUsages = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return $this
     */
    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return $this
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     * @return $this
     */
    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @param string $content
     * @return $this
     */
    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return Collection<int, TechnologyUsage>
     */
    public function getTechnologyUsages(): Collection
    {
        return $this->technologyUsages;
    }

    /**
     * @param TechnologyUsage $technologyUsage
     * @return $this
     */
    public function addTechnologyUsage(TechnologyUsage $technologyUsage): self
    {
        if (!$this->technologyUsages->contains($technologyUsage)) {
            $this->technologyUsages[] = $technologyUsage;
            $technologyUsage->setContent($this);
        }

        return $this;
    }

    /**
     * @return array<Technology>
     */
    public function getTechnologies (): array
    {
        return $this->getTechnologyUsages()->map(fn(TechnologyUsage $usage) => $usage->getTechnology())->toArray();
    }

    /**
     * @return array<Technology>
     */
    public function getMainTechnologies(): array
    {
        return $this->getFilteredTechnology(false);
    }

    /**
     * @return array<Technology>
     */
    public function getSecondaryTechnologies(): array
    {
        return $this->getFilteredTechnology(true);
    }

    /**
     * Synchronise les technologies à partir d'un tableau de technology avec des valeurs de version
     * et de secondary hydraté.
     */
    public function syncTechnologies(array $technologies): self
    {
        $currentTechnologies = $this->getTechnologies();

        // On commence par synchronisé les usages
        /** @var TechnologyUsage $usage */
        foreach($this->getTechnologyUsages() as $usage) {
            $usage->setVersion($usage->getTechnology()->getVersion());
            $usage->setSecondary($usage->getTechnology()->isSecondary());
        }

        // On ajoute les nouveaux usage
        /** @var Technology[] $newUsage */
        $newUsage = array_diff($technologies, $currentTechnologies);
        foreach($newUsage as $technology) {
            $usage = (new TechnologyUsage())
                ->setSecondary($technology->isSecondary())
                ->setTechnology($technology)
                ->setVersion($technology->getVersion());
            $this->addTechnologyUsage($usage);
        }

        // On supprime les technologies qui n'existe pas dans notre nouvelle liste
        $this->technologyUsages = new ArrayCollection($this->technologyUsages->filter(fn (TechnologyUsage $technologyUsage) => in_array($technologyUsage->getTechnology(), $technologies))->getValues());

        return $this;
    }

    /**
     * @return array<Technology>
     */
    private function getFilteredTechnology(bool $secondary = false): array
    {
        $technologies = [];
        foreach ($this->getTechnologyUsages() as $usage) {
            if ($usage->getSecondary() === $secondary) {
                $technologies[] = $usage->getTechnology()->setVersion($usage->getVersion());
            }
        }
        return $technologies;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTimeInterface|null $createdAt
     * @return $this
     */
    public function setCreatedAt(?\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    /**
     * @param \DateTimeInterface|null $updated_at
     * @return $this
     */
    public function setUpdatedAt(?\DateTimeInterface $updated_at): self
    {
        $this->updated_at = $updated_at;
        return $this;
    }

    /**
     * @return bool
     */
    public function isOnline(): bool
    {
        return $this->online;
    }

    /**
     * @param bool $online
     * @return $this
     */
    public function setOnline(bool $online): self
    {
        $this->online = $online;
        return $this;
    }

    public function getImage(): ?Attachment
    {
        return $this->image;
    }

    /**
     * @param Attachment|null $image
     * @return $this
     */
    public function setImage(?Attachment $image): self
    {
        $this->image = $image;
        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    /**
     * @param User $author
     * @return $this
     */
    public function setAuthor(?User $author): self
    {
        $this->author = $author;
        return $this;
    }


}
