<?php

namespace App\Domain\Course\Entity;

class Chapter
{

    private string $title;

    /**
     * @var Course[]
     */
    private array $courses = [];

    /**
     * Génère des chapitres à partir du JSON renvoyée par la base
     *
     * @return Chapter[]
     */
    public static function makeFromFormation(Formation $formation): array
    {
        $formationsById = $formation->getCoursesById();
        $chapters = [];
        foreach ($formation->getRawChapters() as $c) {
            $chapter = new self();
            $chapter->title = $c['title'];
            $chapter->courses = array_map(function (int $id) use ($formationsById) {
                return $formationsById[$id];
            }, $c['courses']);
            $chapters[] = $chapter;
        }
        return $chapters;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): Chapter
    {
        $this->title = $title;
        return $this;
    }

    public function getCourses(): array
    {
        return $this->courses;
    }

    public function setCourses(array $courses): Chapter
    {
        $this->courses = $courses;
        return $this;
    }

}
