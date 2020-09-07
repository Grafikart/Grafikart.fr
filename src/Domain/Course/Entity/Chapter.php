<?php

namespace App\Domain\Course\Entity;

use App\Domain\Application\Entity\Content;

class Chapter
{
    private string $title;

    /**
     * @var array<Content>
     */
    private array $modules = [];

    /**
     * Génère des chapitres à partir du JSON renvoyée par la base.
     *
     * @return Chapter[]
     */
    public static function makeFromContent(Content $target): array
    {
        if ($target instanceof Formation) {
            $modulesById = $target->getCoursesById();
        } elseif ($target instanceof Cursus) {
            $modulesById = $target->getModulesById();
        } else {
            throw new \RuntimeException('Type innattendu');
        }
        $chapters = [];
        foreach ($target->getRawChapters() as $c) {
            $chapter = new self();
            $chapter->title = $c['title'];
            $chapter->modules = array_map(fn (int $id) => $modulesById[$id], $c['modules']);
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

    /**
     * @return Content[]
     */
    public function getModules(): array
    {
        return $this->modules;
    }

    /**
     * @param Content[] $content
     */
    public function setModules(array $content): Chapter
    {
        $this->modules = $content;

        return $this;
    }
}
