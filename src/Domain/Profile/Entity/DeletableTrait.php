<?php

namespace App\Domain\Profile\Entity;

use Doctrine\ORM\Mapping as ORM;

trait DeletableTrait
{

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @var \DateTimeInterface|null
     */
    private ?\DateTimeInterface $deleteAt;

    public function getDeleteAt(): ?\DateTimeInterface
    {
        return $this->deleteAt;
    }

    public function setDeleteAt(?\DateTimeInterface $deleteAt): self
    {
        $this->deleteAt = $deleteAt;

        return $this;
    }
}
