<?php

namespace App\Http\Admin\Data;

use App\Domain\Attachment\Attachment;
use App\Domain\Auth\User;
use App\Domain\Course\Entity\Course;
use App\Http\Admin\Form\CourseForm;
use App\Validator\Exists;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class CourseCrudData implements CrudDataInterface
{

    public string $title = '';

    public string $slug = '';

    public User $author;

    public \DateTimeInterface $createdAt;

    public bool $online = false;

    public bool $source = false;

    public bool $premium = false;

    public ?Attachment $image = null;

    public ?Attachment $youtubeThumbnail = null;

    public ?string $demo = null;

    public ?string $youtube = null;

    public ?string $videoPath = null;

    /**
     * @Exists(class="App\Domain\Course\Entity\Course")
     */
    public ?int $deprecatedBy = null;

    public ?UploadedFile $sourceFile = null;

    public string $content = '';

    public Course $entity;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public static function makeFromCourse(Course $course): self
    {
        $data = new self();
        $data->entity = $course;
        $data->title = $course->getTitle();
        $data->slug = $course->getSlug();
        $data->author = $course->getAuthor();
        $data->createdAt = $course->getCreatedAt();
        $data->videoPath = $course->getVideoPath();
        $data->image = $course->getImage();
        $data->demo = $course->getDemo();
        $data->online = $course->isOnline();
        $data->source = $course->getSource();
        $data->premium = $course->getPremium();
        $data->content = $course->getContent();
        return $data;
    }

    public function hydrate(Course $course): Course
    {
        $course->setTitle($this->title);
        $course->setSlug($this->slug);
        $course->setAuthor($this->author);
        $course->setCreatedAt($this->createdAt);
        $course->setVideoPath($this->videoPath);
        $course->setImage($this->image);
        $course->setDemo($this->demo);
        $course->setOnline($this->online);
        $course->setSource($this->source);
        $course->setPremium($this->premium);
        $course->setContent($this->content);
        $course->setUpdatedAt(new \DateTime());
        return $course;
    }

    public function getEntity(): Course
    {
        return $this->entity;
    }

    public function getFormClass(): string
    {
        return CourseForm::class;
    }
}
