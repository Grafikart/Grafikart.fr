<?php

namespace App\Domain\Badge\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Domain\Badge\Repository\BadgeRepository")
 */
class Badge
{

    public const REQUEST_UNLOCKABLE = ['gamer'];
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer")
     */
    private ?int $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private string $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private string $description;

    /**
     * @ORM\Column(type="integer", options={"default": 0})
     */
    private int $position = 0;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private string $action;

    /**
     * @ORM\Column(type="integer", options={"default": 0})
     */
    private int $actionCount = 0;

    /**
     * @ORM\Column(type="string", length=255, options={"default": "grey"})
     */
    private string $theme = 'grey';

    public function __construct(string $name, string $description, string $action, int $actionCount = 0)
    {
        $this->name = $name;
        $this->description = $description;
        $this->action = $action;
        $this->actionCount = $actionCount;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): Badge
    {
        $this->id = $id;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): Badge
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): Badge
    {
        $this->description = $description;

        return $this;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function setPosition(int $position): Badge
    {
        $this->position = $position;

        return $this;
    }

    public function getAction(): string
    {
        return $this->action;
    }

    public function setAction(string $action): Badge
    {
        $this->action = $action;

        return $this;
    }

    public function getActionCount(): int
    {
        return $this->actionCount;
    }

    public function setActionCount(int $actionCount): Badge
    {
        $this->actionCount = $actionCount;

        return $this;
    }

    public function getTheme(): string
    {
        return $this->theme;
    }

    public function setTheme(string $theme): Badge
    {
        $this->theme = $theme;

        return $this;
    }
}
