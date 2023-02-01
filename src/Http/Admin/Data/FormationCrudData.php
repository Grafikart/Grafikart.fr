<?php

namespace App\Http\Admin\Data;

use App\Domain\Attachment\Attachment;
use App\Domain\Auth\User;
use App\Domain\Course\Entity\Chapter;
use App\Domain\Course\Entity\Course;
use App\Domain\Course\Entity\Formation;
use App\Domain\Course\Entity\Technology;
use App\Http\Form\AutomaticForm;
use App\Validator\Exists;
use App\Validator\Slug;
use App\Validator\Unique;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[Unique(field: 'slug')]
class FormationCrudData implements CrudDataInterface
{
    private ?EntityManagerInterface $em = null;

    #[Assert\NotBlank]
    public ?string $title;

    #[Slug]
    #[Assert\NotBlank]
    public ?string $slug;

    public \DateTimeInterface $createdAt;

    public ?User $author;

    public ?string $youtubePlaylist;

    public bool $online;

    public ?Attachment $image;

    /**
     * @var Technology[]
     */
    public array $mainTechnologies = [];

    /**
     * @var Technology[]
     */
    public array $secondaryTechnologies = [];

    public ?string $content;

    public ?string $short;

    public ?string $links;

    public int $level;

    /**
     * @var Chapter[]
     */
    public array $chapters;

    #[Exists(class: Formation::class)]
    public ?int $deprecatedBy = null;

    public bool $forceRedirect = false;

    public function __construct(private readonly Formation $formation)
    {
        $this->title = $formation->getTitle();
        $this->slug = $formation->getSlug();
        $this->author = $formation->getAuthor();
        $this->createdAt = $formation->getCreatedAt();
        $this->youtubePlaylist = $formation->getYoutubePlaylist();
        $this->online = $formation->isOnline();
        $this->image = $formation->getImage();
        $this->mainTechnologies = $formation->getMainTechnologies();
        $this->secondaryTechnologies = $formation->getSecondaryTechnologies();
        $this->short = $formation->getShort();
        $this->content = $formation->getContent();
        $this->chapters = $formation->getChapters();
        $this->links = $formation->getLinks();
        $this->level = $formation->getLevel();
        $deprecatedBy = $formation->getDeprecatedBy();
        $this->forceRedirect = $formation->isForceRedirect();
        $this->deprecatedBy = $deprecatedBy?->getId();
    }

    public function getEntity(): Formation
    {
        return $this->formation;
    }

    public function getFormClass(): string
    {
        return AutomaticForm::class;
    }

    public function hydrate(): void
    {
        $this->formation->setTitle($this->title);
        $this->formation->setSlug($this->slug);
        $this->formation->setCreatedAt($this->createdAt);
        $this->formation->setUpdatedAt(new \DateTime());
        $this->formation->setAuthor($this->author);
        $this->formation->setYoutubePlaylist($this->youtubePlaylist);
        $this->formation->setOnline($this->online);
        $this->formation->setImage($this->image);
        $this->formation->setShort($this->short);
        $this->formation->setContent($this->content);
        $this->formation->setLinks($this->links);
        $this->formation->setLevel($this->level);
        $this->formation->setForceRedirect($this->forceRedirect);
        if ($this->em) {
            $deprecatedBy = $this->deprecatedBy;
            $this->formation->setDeprecatedBy($deprecatedBy ? $this->em->find(Formation::class, $deprecatedBy) : null);
        }
        foreach ($this->mainTechnologies as $technology) {
            $technology->setSecondary(false);
        }
        foreach ($this->secondaryTechnologies as $technology) {
            $technology->setSecondary(true);
        }
        $removed = $this->formation->syncTechnologies(array_merge($this->mainTechnologies, $this->secondaryTechnologies));
        if ($this->em) {
            foreach ($removed as $usage) {
                $this->em->remove($usage);
            }
        }
        /** @var Course $course */
        foreach ($this->formation->getCourses() as $course) {
            $course->setFormation(null);
        }
        foreach ($this->chapters as $chapter) {
            /** @var Course $course */
            foreach ($chapter->getModules() as $course) {
                $course->setFormation($this->formation);
            }
        }
        $this->formation->setChapters($this->chapters);
    }

    public function setEntityManager(EntityManagerInterface $em): self
    {
        $this->em = $em;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->getEntity()->getId();
    }
}
