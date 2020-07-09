<?php

namespace App\Domain\Course\Utils;

use App\Core\Twig\BreadcrumbInterface;
use App\Domain\Course\Entity\Course;

class CourseBreadcrumb implements BreadcrumbInterface
{
    /**
     * @param Course $course
     */
    public function generate($course): array
    {
        $items = [];
        $items['Tutoriels'] = ['course_index'];
        foreach ($course->getMainTechnologies() as $technology) {
            $items[$technology->getName()] = ['technology_show', ['slug' => $technology->getSlug()]];
        }
        if ($formation = $course->getFormation()) {
            $items[$formation->getTitle()] = ['formation_show', ['slug' => $formation->getSlug()]];
        }
        $items[$course->getTitle()] = ['course_show', [
            'id' => $course->getId(),
            'slug' => $course->getSlug(),
        ]];

        return $items;
    }

    public function support(object $object): bool
    {
        return $object instanceof Course;
    }
}
