<?php

namespace App\Domain\Course\Entity;

use App\Domain\Application\Entity\Content;
use Doctrine\ORM\Mapping as ORM;

trait ChapterableTrait
{
    /**
     * @ORM\Column(type="json")
     *
     * @return array{title: string, content: int[]}[]
     */
    protected array $chapters = [];

    /**
     * @param list<array{title: string, modules: int[]}> $chapters
     */
    public function setRawChapters(array $chapters): self
    {
        $this->chapters = $chapters;

        return $this;
    }

    /**
     * Initialise les chapitres depuis le JSON.
     *
     * @return Chapter[]
     */
    public function getChapters(): array
    {
        return Chapter::makeFromContent($this);
    }

    /**
     * Renvoie les données brut (JSON).
     */
    public function getRawChapters(): array
    {
        return $this->chapters;
    }

    /**
     * Rempli le champs JSON à partir d'un tableau d'objet chapitres.
     *
     * @var Chapter[]
     */
    public function setChapters(array $chapters): self
    {
        $this->chapters = array_map(function (Chapter $chapter) {
            return [
                'title' => $chapter->getTitle(),
                'modules' => array_map(fn (Content $course) => $course->getId(), $chapter->getModules()),
            ];
        }, $chapters);

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
