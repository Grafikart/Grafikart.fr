<?php

namespace App\Domain\Course\Utils;

use App\Domain\Course\Entity\Course;
use App\Normalizer\Breadcrumb\BreadcrumbGeneratorInterface;
use App\Normalizer\Breadcrumb\BreadcrumbItem;

class CourseBreadcrumbGenerator implements BreadcrumbGeneratorInterface
{
    /**
     * @param Course $course
     */
    public function generate($course): array
    {
        $items = [];
        $items[] = new BreadcrumbItem('Tutoriels', ['course_index']);
        $categories = [];
        foreach ($course->getMainTechnologies() as $technology) {
            $categories[] = new BreadcrumbItem(
                (string) $technology->getName(),
                ['technology_show', ['slug' => $technology->getSlug()]]
            );
        }
        if (count($categories) > 0) {
            $items[] = $categories;
        }
        if ($formation = $course->getFormation()) {
            $items[] = new BreadcrumbItem(
                (string) $formation->getTitle(),
                ['formation_show', ['slug' => $formation->getSlug()]]
            );
        }
        $items[] = new BreadcrumbItem((string) $course->getTitle(), ['course_show', [
            'id' => $course->getId(),
            'slug' => $course->getSlug(),
        ]]);

        return $items;
    }

    public function support(object $object): bool
    {
        return $object instanceof Course;
    }
}
