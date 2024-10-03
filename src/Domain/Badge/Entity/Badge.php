<?php

namespace App\Domain\Badge\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[Vich\Uploadable]
#[ORM\Entity(repositoryClass: \App\Domain\Badge\Repository\BadgeRepository::class)]
class Badge
{
    final public const REQUEST_UNLOCKABLE = ['gamer', 'lochness'];

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'SEQUENCE')]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'integer', options: ['default' => 0])]
    private int $position = 0;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $image = null;

    #[Vich\UploadableField(mapping: 'badges', fileNameProperty: 'image')]
    private ?File $imageFile = null;

    #[ORM\Column(type: 'boolean', options: ['default' => 0])]
    private bool $unlockable = false;

    #[ORM\Column(type: 'datetime', nullable: false)]
    private \DateTimeInterface $updatedAt;

    public function __construct(
        #[ORM\Column(type: 'string', length: 255, nullable: false)]
        private string $name = '',
        #[ORM\Column(type: 'string', length: 255, nullable: false)]
        private string $description = '',
        #[ORM\Column(type: 'string', length: 255, nullable: false)]
        private string $action = '',
        #[ORM\Column(type: 'integer', options: ['default' => 0])]
        private int $actionCount = 0,
        #[ORM\Column(type: 'string', length: 255, options: ['default' => 'grey'])]
        private string $theme = 'grey'
    ) {
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

    public function isUnlockable(): bool
    {
        return $this->unlockable;
    }

    public function setUnlockable(bool $unlockable): Badge
    {
        $this->unlockable = $unlockable;

        return $this;
    }
}
