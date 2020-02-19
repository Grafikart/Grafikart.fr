<?php

namespace App\Domain\Attachment;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity()
 * @Vich\Uploadable
 */
class Attachment
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer")
     */
    protected int $id = 0;

    /**
     * @Vich\UploadableField(mapping="attachments", fileNameProperty="fileName", size="fileSize")
     * @var File|null
     */
    private ?File $file;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $fileName = '';

    /**
     * @ORM\Column(type="integer", options={"unsigned"=true})
     */
    private int $fileSize = 0;

    /**
     * @ORM\Column(type="datetime")
     */
    private \DateTimeInterface $createdAt;

    public function getId(): int
    {
        return $this->id;
    }

    public function getFileName(): string
    {
        return $this->fileName;
    }

    public function setFileName(?string $fileName): self
    {
        $this->fileName = $fileName ?: '';
        return $this;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getFile(): ?File
    {
        return $this->file;
    }

    public function setFile(?File $file): Attachment
    {
        $this->file = $file;
        return $this;
    }

    public function getFileSize(): int
    {
        return $this->fileSize;
    }

    public function setFileSize(?int $fileSize): Attachment
    {
        $this->fileSize = $fileSize ?: 0;
        return $this;
    }

    public function __toString()
    {
        return $this->fileName;
    }

}
