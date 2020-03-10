<?php

namespace App\Http\Admin\Data;

use App\Domain\Attachment\Attachment;
use App\Domain\Auth\User;
use App\Domain\Course\Entity\Course;
use App\Domain\Course\Entity\Technology;
use App\Http\Form\AutomaticForm;
use App\Validator\Exists;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

class CourseCrudData implements CrudDataInterface
{

    public string $title = '';

    public string $slug = '';

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

    public string $content = '';

    private Course $entity;

    public ?int $duration = 0;

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

    public function __construct(Course $course)
    {
        $this->entity = $course;
        $this->title = $course->getTitle();
        $this->slug = $course->getSlug();
        $this->author = $course->getAuthor();
        $this->createdAt = $course->getCreatedAt() ?: new \DateTime();
        $this->videoPath = $course->getVideoPath();
        $this->image = $course->getImage();
        $this->demo = $course->getDemo();
        $this->online = $course->isOnline();
        $this->premium = $course->getPremium();
        $this->content = $course->getContent();
        $this->youtube = $course->getYoutubeId();
        $this->duration = $course->getDuration();
        $this->source = !empty($course->getSource());
        $this->mainTechnologies = $course->getMainTechnologies();
        $this->secondaryTechnologies = $course->getSecondaryTechnologies();
    }

    public function hydrate(): void
    {
        $this->entity->setTitle($this->title);
        $this->entity->setSlug($this->slug);
        $this->entity->setAuthor($this->author);
        $this->entity->setVideoPath($this->videoPath);
        $this->entity->setImage($this->image);
        $this->entity->setDemo($this->demo);
        $this->entity->setOnline($this->online);
        $this->entity->setSourceFile($this->sourceFile);
        $this->entity->setPremium($this->premium);
        $this->entity->setContent($this->content);
        $this->entity->setCreatedAt($this->createdAt);
        $this->entity->setUpdatedAt(new \DateTime());
        foreach($this->mainTechnologies as $technology) {
            $technology->setSecondary(false);
        }
        foreach($this->secondaryTechnologies as $technology) {
            $technology->setSecondary(true);
        }
        $this->entity->syncTechnologies(array_merge($this->mainTechnologies, $this->secondaryTechnologies));
        dd(array_merge($this->mainTechnologies, $this->secondaryTechnologies), $this->entity->getTechnologyUsages()->toArray());
    }

    public function getEntity(): Course
    {
        return $this->entity;
    }

    public function getFormClass(): string
    {
        return AutomaticForm::class;
    }
}
