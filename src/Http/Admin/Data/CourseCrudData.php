<?php

namespace App\Http\Admin\Data;

use App\Domain\Attachment\Attachment;
use App\Domain\Auth\User;
use App\Domain\Course\Entity\Course;
use App\Http\Admin\Form\CourseForm;
use App\Validator\Exists;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @method  hydrate(object $post, EntityManagerInterface $em)
 */
class CourseCrudData implements CrudDataInterface
{

    public ?string $title = '';

    public ?string $slug = '';

    public ?User $author = null;

    public ?\DateTimeInterface $createdAt = null;

    public ?bool $online = false;

    public ?bool $source = false;

    public ?bool $premium = false;

    public ?Attachment $image = null;

    public ?Attachment $youtubeThumbnail = null;

    public ?string $demo = '';

    public ?string $youtube = '';

    public ?string $videoPath = '';

    /**
     * @Exists(class="App\Domain\Course\Entity\Course")
     */
    public ?int $deprecatedBy = null;

    public ?UploadedFile $sourceFile = null;

    public ?string $content = '';

    private Course $entity;


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
        $data->deprecatedBy = 123123123;
        return $data;
    }

    public function getEntity(): Course
    {
        return $this->entity;
    }

    public function getFormClass(): string
    {
        return CourseForm::class;
    }

    public function hydrate(Course $course): Course
    {
        return $course;
    }
}
