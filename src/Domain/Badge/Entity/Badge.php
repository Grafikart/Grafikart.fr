<?php

namespace App\Domain\Badge\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass="App\Domain\Badge\Repository\BadgeRepository")
 * @Vich\Uploadable()
 */
class Badge
{
    public const REQUEST_UNLOCKABLE = ['gamer', 'lochness'];

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

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $image = null;

    /**
     * @Vich\UploadableField(fileNameProperty="image", mapping="badges")
     */
    private ?File $imageFile = null;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    private \DateTimeInterface $updatedAt;

    public function __construct(string $name = '', string $description = '', string $action = '', int $actionCount = 0, string $theme = 'grey')
    {
        $this->name = $name;
        $this->description = $description;
        $this->action = $action;
        $this->actionCount = $actionCount;
        $this->theme = $theme;
        $this->updatedAt = new \DateTimeImmutable();
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

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): Badge
    {
        $this->image = $image;

        return $this;
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function setImageFile(?File $imageFile): Badge
    {
        $this->imageFile = $imageFile;

        return $this;
    }

    public function getUpdatedAt(): \DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): Badge
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function imageName(): string
    {
        return $this->action.'-'.$this->actionCount;
    }
}
