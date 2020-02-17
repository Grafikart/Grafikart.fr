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
    private int $id = 0;

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
    private \DateTimeInterface $createdAt;

    /**
     * @ORM\Column(type="datetime")
     */
    private \DateTimeInterface $updated_at;

    /**
     * @ORM\Column(type="boolean", options={"default": 0})
     */
    private bool $online = false;

    /**
     * @ORM\ManyToOne(targetEntity="App\Domain\Attachment\Attachment", cascade={"persist"})
     * @ORM\JoinColumn(name="attachment_id", referencedColumnName="id")
     */
    private ?Attachment $image;

    /**
     * @ORM\ManyToOne(targetEntity="App\Domain\Auth\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private User $author;

    /**
     * @ORM\OneToMany(targetEntity="App\Domain\Course\Entity\TechnologyUsage", mappedBy="content", orphanRemoval=true)
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

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getContent(): string
    {
        return $this->content;
    }

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
    public function getMainTechnologies(): array {
        $technologies = [];
        foreach ($this->getTechnologyUsages() as $usage) {
            if ($usage->getSecondary() === false) {
                $technologies[] = $usage->getTechnology()->setVersion($usage->getVersion());
            }
        }
        return $technologies;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): \DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(\DateTimeInterface $updated_at): self
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
     * @return static
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

    public function setImage(?Attachment $image): Content
    {
        $this->image = $image;
        return $this;
    }

    public function getAuthor(): User
    {
        return $this->author;
    }

    public function setAuthor(User $author): Content
    {
        $this->author = $author;
        return $this;
    }

}
