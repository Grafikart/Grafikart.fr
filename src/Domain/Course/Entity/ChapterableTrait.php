<?php

namespace App\Domain\Course\Entity;

use App\Domain\Application\Entity\Content;
use Doctrine\ORM\Mapping as ORM;

trait ChapterableTrait
{
    /**
     * @return array{title: string, content: int[]}[]
     */
    #[ORM\Column(type: 'json')]
    protected array $chapters = [];

    public function setRawChapters(array $chapters): self
    {
        $this->chapters = $chapters;

        return $this;
    }

    /**
     * Initialise les chapitres depuis le JSON.
     */
    public function getChapters(): array
    {
        return Chapter::makeFromContent($this);
    }

    public function getCoursesCount(): int
    {
        return array_reduce($this->chapters, fn (int $carry, array $item) => $carry + count($item['modules']), 0);
    }

    /**
     * Renvoie les données brutes (JSON).
     *
     * @return array{title: string, modules: array<int, int>}[]
     */
    public function getRawChapters(): array
    {
        return $this->chapters;
    }

    /**
     * Rempli le champ JSON à partir d'un tableau d'objet chapitres.
     *
     * @param Chapter[] $chapters
     */
    public function setChapters(array $chapters): self
    {
        $this->chapters = array_map(fn (Chapter $chapter) => [
            'title' => $chapter->getTitle(),
            'modules' => array_map(fn (Content $course) => $course->getId(), $chapter->getModules()),
        ], $chapters);

        return $this;
    }

    /**
     * Extrait le premier contenu du premier chapitre.
     */
    public function getFirstContent(): ?Content
    {
        $firstChapter = $this->getChapters()[0] ?? null;
        if (null === $firstChapter) {
            return null;
        }

        return $firstChapter->getModules()[0] ?? null;
    }

    /**
     * Renvoie la liste des ids des contenu organisés.
     *
     * @return int[]
     */
    public function getModulesIds(): array
    {
        return array_reduce($this->getRawChapters(), fn (array $acc, array $chapter) => array_merge($acc, $chapter['modules']), []);
    }
}
