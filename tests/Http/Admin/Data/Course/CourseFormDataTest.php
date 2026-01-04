<?php

namespace App\Tests\Http\Admin\Data\Course;

use App\Domain\Attachment\Attachment;
use App\Domain\Course\Entity\Course;
use App\Domain\Course\Entity\Technology;
use App\Domain\Course\Entity\TechnologyUsage;
use App\Http\Admin\Data\Course\CourseFormData;
use App\Tests\DTOTestCase;

use const App\Domain\Course\Entity\MEDIUM;

class CourseFormDataTest extends DTOTestCase
{
    private function getInput(): Course
    {
        $image = $this->em->getReference(Attachment::class, 10);
        $youtubeThumbnail = $this->em->getReference(Attachment::class, 11);
        $deprecatedBy = $this->em->getReference(Course::class, 2);

        $course = new Course()
            ->setId(1)
            ->setTitle('Course Title')
            ->setSlug('course-slug')
            ->setCreatedAt(new \DateTimeImmutable())
            ->setOnline(true)
            ->setPremium(true)
            ->setForceRedirect(true)
            ->setVideoPath('video.mp4')
            ->setDemo('https://demo.com')
            ->setYoutubeId('youtube123')
            ->setDuration(120)
            ->setDeprecatedBy($deprecatedBy)
            ->setContent('Course Content')
            ->setImage($image)
            ->setLevel(MEDIUM)
            ->setYoutubeThumbnail($youtubeThumbnail)
            ->setSource('source.zip');

        $technology = (new Technology())->setName('PHP')->setId(5);
        $usage = (new TechnologyUsage())
            ->setTechnology($technology)
            ->setSecondary(false)
            ->setVersion('8.1');
        $course->addTechnologyUsage($usage);

        return $course;
    }

    public function testFromCourse(): void
    {
        $input = $this->getInput();
        $data = $this->transform($input, CourseFormData::class);
        assert($data instanceof CourseFormData);
        /*

        public string $url,
        public int $id,
        public string $title,
        public string $slug,
        public \DateTimeInterface $createdAt,
        public bool $online,
        public bool $premium,
        public bool $forceRedirect,
        public string $videoPath,
        public string $demo,
        public string $youtubeId,
        public int $duration,
        public ?int $deprecatedBy,
        public string $content,
        public int $level,
        public bool $source,
        public array $technologies,
        public ?CourseAttachmentData $image,
        public ?CourseAttachmentData $youtubeThumbnail,
         */
        $this->assertNotEmpty($data->url);
        $this->assertEquals($input->getTitle(), $data->title);


    }
}
