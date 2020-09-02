<?php

namespace App\Domain\Profile\Entity;

use Doctrine\ORM\Mapping as ORM;

trait DeletableTrait
{
    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private ?\DateTimeImmutable $deleteAt;

    public function getDeleteAt(): ?\DateTimeImmutable
    {
        return $this->deleteAt;
    }

    public function setDeleteAt(?\DateTimeImmutable $deleteAt): self
    {
        $this->deleteAt = $deleteAt;

        return $this;
    }
}
