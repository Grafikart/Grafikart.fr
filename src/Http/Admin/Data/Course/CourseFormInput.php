<?php

namespace App\Http\Admin\Data\Course;

use App\Domain\Course\DTO\ContentTechnologyDTO;
use App\Domain\Course\Entity\Course;
use App\Domain\Course\Service\TechnologySyncService;
use App\Validator\Slug;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;
use const App\Domain\Course\Entity\EASY;
use const App\Domain\Course\Entity\HARD;

readonly class CourseFormInput
{
    public function __construct(
        #[NotBlank()]
        public string $title = '',
        #[NotBlank()]
        public string $content = '',
        #[NotBlank()]
        #[Range(min: EASY, max: HARD)]
        public int $level = EASY,
//        public bool $online = false,
//        public bool $premium = false,
//        public bool $redirect = false,
//        public string $videoPath = '',
//        public string $demo = '',
//        public ?int $deprecatedBy = null,
        #[NotBlank]
        #[Slug]
        public string $slug = '',

        #[Image()]
        #[File(extensions: ['jpg', 'png', 'svg'])]
        public ?UploadedFile $image = null,
        /** @var ContentTechnologyDTO[] */
        public array $technologies,
    ) {
    }

    public function hydrateEntity(Course $course, TechnologySyncService $syncService)
    {
        $course->setTitle($this->title);
        $course->setContent($this->content);
        $syncService->sync($course, $this->technologies);
    }

}
