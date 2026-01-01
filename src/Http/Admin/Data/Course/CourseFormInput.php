<?php

namespace App\Http\Admin\Data\Course;

use Symfony\Component\Validator\Constraints\NotBlank;

readonly class CourseFormInput
{
    public function __construct(
        #[NotBlank()]
        public string $title,
        #[NotBlank()]
        public string $content,

        /** @var TechnologyData[] */
        public array $technologies,
    ) {
    }

}
