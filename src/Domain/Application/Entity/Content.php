<?php

namespace App\Domain\Application\Entity;

use App\Domain\Application\Repository\ContentRepository;
use App\Domain\Attachment\Attachment;
use App\Domain\Auth\User;
use App\Domain\Blog\Post;
use App\Domain\Course\Entity\Course;
use App\Domain\Course\Entity\Cursus;
use App\Domain\Course\Entity\Formation;
use App\Domain\Course\Entity\Technology;
use App\Domain\Course\Entity\TechnologyUsage;
use App\Http\Twig\CacheExtension\CacheableInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ContentRepository::class)]
#[ORM\InheritanceType('JOINED')]
#[ORM\DiscriminatorColumn(name: 'type', type: 'string')]
#[ORM\DiscriminatorMap(['course' => Course::class, 'formation' => Formation::class, 'post' => Post::class, 'cursus' => Cursus::class])]
abstract class Content implements CacheableInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue()]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $slug = null;

    #[ORM\Column(type: 'text')]
    private ?string $content = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeInterface $updated_at = null;

    #[ORM\Column(type: 'boolean', options: ['default' => 0])]
    private bool $online = false;

    #[ORM\ManyToOne(targetEntity: Attachment::class, cascade: ['persist'])]
    #[ORM\JoinColumn(name: 'attachment_id', referencedColumnName: 'id')]
    private ?Attachment $image = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id')]
    private ?User $author = null;

    /**
     * @var Collection<int, TechnologyUsage>
     */
    #[ORM\OneToMany(targetEntity: TechnologyUsage::class, mappedBy: 'content', cascade: ['persist'])]
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
     * @return $this
     */
    public function setId(?int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @return $this
     */
    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    /**
     * @return $this
     */
    public function setSlug(?string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function getExcerpt(): string
    {
        if (null === $this->content) {
            return '';
        }

        $parts = preg_split("/(\r\n|\r|\n){2}/", $this->content);

        return false === $parts ? '' : strip_tags($parts[0]);
    }

    /**
     * @return $this
     */
    public function setContent(?string $content): self
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
     * @return Technology[]
     */
    public function getTechnologies(): array
    {
        return $this->getTechnologyUsages()->map(fn (TechnologyUsage $usage) => $usage->getTechnology())->toArray();
    }

    /**
     * @return Technology[]
     */
    public function getMainTechnologies(): array
    {
        return $this->getFilteredTechnology(false);
    }

    /**
     * @return Technology[]
     */
    public function getSecondaryTechnologies(): array
    {
        return $this->getFilteredTechnology(true);
    }

    /**
     * Synchronise les technologies à partir d'un tableau de technology avec des valeurs de version
     * et de secondary hydraté.
     *
     * @return array<TechnologyUsage> Relation TechnologyUsage détachés de l'entité (qu'il faudra supprimer)
     */
    public function syncTechnologies(array $technologies): array
    {
        $currentTechnologies = $this->getTechnologies();

        // On commence par synchronisé les usages
        /** @var TechnologyUsage $usage */
        foreach ($this->getTechnologyUsages() as $usage) {
            $usage->setVersion($usage->getTechnology()->getVersion());
            $usage->setSecondary($usage->getTechnology()->isSecondary());
        }

        // On ajoute les nouveaux usage
        /** @var Technology[] $newUsage */
        $newUsage = array_diff($technologies, $currentTechnologies);
        foreach ($newUsage as $technology) {
            $usage = (new TechnologyUsage())
                ->setSecondary($technology->isSecondary())
                ->setTechnology($technology)
                ->setVersion($technology->getVersion());
            $this->addTechnologyUsage($usage);
        }

        // On supprime les technologies qui n'existe pas dans notre nouvelle liste
        $removed = [];
        $newUsage = [];
        foreach ($this->technologyUsages as $usage) {
            if (!in_array($usage->getTechnology(), $technologies)) {
                $removed[] = $usage;
            } else {
                $newUsage[] = $usage;
            }
        }
        $this->technologyUsages = new ArrayCollection($newUsage);

        return $removed;
    }

    /**
     * @return Technology[]
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

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt ?: new \DateTimeImmutable();
    }

    /**
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
     * @return $this
     */
    public function setUpdatedAt(?\DateTimeInterface $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    public function isOnline(): bool
    {
        return $this->online;
    }

    /**
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
     * @return $this
     */
    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function isCourse(): bool
    {
        return $this instanceof Course;
    }

    public function isFormation(): bool
    {
        return $this instanceof Formation;
    }

    /**
     * Renvoie le nom du fichier pour le téléchargement des sources / vidéo.
     */
    public function getFilename(): string
    {
        return str_replace(['.', ',', ':'], [' ', '', ''], $this->title ?: '');
    }

    public function isScheduled(): bool
    {
        return new \DateTimeImmutable() < $this->getCreatedAt();
    }

    public function hasYoutubeLink(): bool
    {
        return str_contains($this->content ?? '', 'youtube.com/');
    }
}
