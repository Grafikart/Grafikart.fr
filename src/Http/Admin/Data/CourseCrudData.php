<?php

namespace App\Http\Admin\Data;

use App\Domain\Attachment\Attachment;
use App\Domain\Auth\User;
use App\Domain\Course\Entity\Course;
use const App\Domain\Course\Entity\MEDIUM;
use App\Domain\Course\Entity\Technology;
use App\Http\Form\AutomaticForm;
use App\Validator\Exists;
use App\Validator\Slug;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

class CourseCrudData implements CrudDataInterface
{
    /**
     * @Assert\NotBlank()
     */
    public ?string $title = null;

    /**
     * @Assert\NotBlank()
     * @Slug()
     */
    public ?string $slug = null;

    public ?User $author;

    public \DateTimeInterface $createdAt;

    public bool $online = false;

    public bool $source = false;

    public bool $premium = false;

    public ?string $demo = null;

    public ?string $youtube = null;

    public ?string $videoPath = null;

    /**
     * @Exists(class="App\Domain\Course\Entity\Course")
     */
    public ?int $deprecatedBy = null;

    public ?string $content = null;

    public ?int $duration = 0;

    public int $level = MEDIUM;

    public ?Attachment $image = null;

    public ?Attachment $youtubeThumbnail = null;

    /**
     * @Assert\File(mimeTypes={"application/zip"})
     */
    public ?UploadedFile $sourceFile = null;

    /**
     * @var Technology[]
     */
    public array $mainTechnologies = [];

    /**
     * @var Technology[]
     */
    public array $secondaryTechnologies = [];

    private EntityManagerInterface $em;

    public function __construct(private Course $entity)
    {
        $this->title = $entity->getTitle();
        $this->slug = $entity->getSlug();
        $this->author = $entity->getAuthor();
        $this->createdAt = $entity->getCreatedAt();
        $this->videoPath = $entity->getVideoPath();
        $this->image = $entity->getImage();
        $this->demo = $entity->getDemo();
        $this->online = $entity->isOnline();
        $this->premium = $entity->getPremium();
        $this->content = $entity->getContent();
        $this->youtube = $entity->getYoutubeId();
        $this->duration = $entity->getDuration();
        $this->source = !empty($entity->getSource());
        $this->mainTechnologies = $entity->getMainTechnologies();
        $this->secondaryTechnologies = $entity->getSecondaryTechnologies();
        $this->youtubeThumbnail = $entity->getYoutubeThumbnail();
        $this->duration = $entity->getDuration();
        $deprecatedBy = $entity->getDeprecatedBy();
        $this->deprecatedBy = $deprecatedBy ? $deprecatedBy->getId() : null;
        $this->level = $entity->getLevel();
    }

    public function hydrate(): void
    {
        $this->entity->setTitle($this->title);
        $this->entity->setSlug($this->slug);
        $this->entity->setAuthor($this->author);
        $deprecatedBy = $this->deprecatedBy;
        $this->entity->setDeprecatedBy($deprecatedBy ? $this->em->find(Course::class, $deprecatedBy) : null);
        $this->entity->setVideoPath($this->videoPath);
        $this->entity->setImage($this->image);
        $this->entity->setYoutubeThumbnail($this->youtubeThumbnail);
        $this->entity->setDemo($this->demo);
        $this->entity->setOnline($this->online);
        $this->entity->setSourceFile($this->sourceFile);
        $this->entity->setYoutubeId($this->youtube);
        $this->entity->setPremium($this->premium);
        $this->entity->setContent($this->content);
        $this->entity->setCreatedAt($this->createdAt);
        $this->entity->setUpdatedAt(new \DateTime());
        $this->entity->setLevel($this->level);
        foreach ($this->mainTechnologies as $technology) {
            $technology->setSecondary(false);
        }
        foreach ($this->secondaryTechnologies as $technology) {
            $technology->setSecondary(true);
        }
        $removed = $this->entity->syncTechnologies(array_merge($this->mainTechnologies, $this->secondaryTechnologies));
        if ($this->entity->getId()) {
            foreach ($removed as $usage) {
                $this->em->remove($usage);
            }
        }
    }

    public function getEntity(): Course
    {
        return $this->entity;
    }

    public function getFormClass(): string
    {
        return AutomaticForm::class;
    }

    public function setEntityManager(EntityManagerInterface $em): self
    {
        $this->em = $em;

        return $this;
    }
}
