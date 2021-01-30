<?php

namespace App\Http\Admin\Data;

use App\Domain\Attachment\Attachment;
use App\Domain\Auth\User;
use App\Domain\Course\Entity\Chapter;
use App\Domain\Course\Entity\Cursus;
use App\Domain\Course\Entity\CursusCategory;
use App\Domain\Course\Entity\Technology;
use App\Http\Form\AutomaticForm;
use App\Validator\Slug;
use App\Validator\Unique;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @Unique(field="slug")
 */
class CursusCrudData implements CrudDataInterface
{
    private Cursus $cursus;

    private ?EntityManagerInterface $em = null;

    /**
     * @Assert\NotBlank()
     */
    public ?string $title;

    /**
     * @Assert\NotBlank()
     * @Slug()
     */
    public ?string $slug;

    public \DateTimeInterface $createdAt;

    public ?User $author;

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

    /**
     * @var Chapter[]
     */
    public array $chapters;

    public ?CursusCategory $category;

    public function __construct(Cursus $cursus)
    {
        $this->cursus = $cursus;
        $this->title = $cursus->getTitle();
        $this->slug = $cursus->getSlug();
        $this->author = $cursus->getAuthor();
        $this->createdAt = $cursus->getCreatedAt();
        $this->online = $cursus->isOnline();
        $this->image = $cursus->getImage();
        $this->mainTechnologies = $cursus->getMainTechnologies();
        $this->secondaryTechnologies = $cursus->getSecondaryTechnologies();
        $this->content = $cursus->getContent();
        $this->chapters = $cursus->getChapters();
        $this->category = $cursus->getCategory();
    }

    public function getEntity(): Cursus
    {
        return $this->cursus;
    }

    public function getFormClass(): string
    {
        return AutomaticForm::class;
    }

    public function hydrate(): void
    {
        $this->cursus->setTitle($this->title);
        $this->cursus->setSlug($this->slug);
        $this->cursus->setCreatedAt($this->createdAt);
        $this->cursus->setUpdatedAt(new \DateTime());
        $this->cursus->setAuthor($this->author);
        $this->cursus->setOnline($this->online);
        $this->cursus->setImage($this->image);
        $this->cursus->setContent($this->content);
        if ($this->category) {
            $this->cursus->setCategory($this->category);
        }
        foreach ($this->mainTechnologies as $technology) {
            $technology->setSecondary(false);
        }
        foreach ($this->secondaryTechnologies as $technology) {
            $technology->setSecondary(true);
        }
        $removed = $this->cursus->syncTechnologies(array_merge($this->mainTechnologies, $this->secondaryTechnologies));
        if ($this->em) {
            foreach ($removed as $usage) {
                $this->em->remove($usage);
            }
        }

        $chapterModulesIds = [];
        foreach ($this->chapters as $chapter) {
            foreach ($chapter->getModules() as $module) {
                $this->cursus->addModule($module);
                $chapterModulesIds[] = $module->getId();
            }
        }
        foreach ($this->cursus->getModules() as $module) {
            if (!in_array($module->getId(), $chapterModulesIds)) {
                $this->cursus->removeModule($module);
            }
        }
        $this->cursus->setChapters($this->chapters);
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
