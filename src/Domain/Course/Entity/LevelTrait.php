<?php

namespace App\Domain\Course\Entity;

use Doctrine\ORM\Mapping as ORM;

const EASY = 0;
const MEDIUM = 1;
const HARD = 2;

trait LevelTrait
{
    #[ORM\Column(type: 'smallint', options: ['default' => 0])]
    private int $level = 0;

    public static array $levels = [
        EASY => 'Junior',
        MEDIUM => 'Intermédiaire',
        HARD => 'Senior',
    ];

    public static array $colors = [
        EASY => 'green',
        MEDIUM => 'yellow',
        HARD => 'red',
    ];

    public function getLevel(): int
    {
        return $this->level;
    }

    public function getLevelName(): string
    {
        return self::$levels[$this->level];
    }

    public function getLevelColor(): string
    {
        return self::$colors[$this->level];
    }

    public function setLevel(int $level): self
    {
        $this->level = $level;

        return $this;
    }
}
