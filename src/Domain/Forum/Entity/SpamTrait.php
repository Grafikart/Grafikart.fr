<?php

namespace App\Domain\Forum\Entity;

use Doctrine\ORM\Mapping as ORM;

trait SpamTrait
{
    /**
     * @ORM\Column(type="boolean", options={"default" : 1})
     */
    private bool $spam = false;

    public function isSpam(): bool
    {
        return $this->spam;
    }

    public function setSpam(bool $spam): self
    {
        $this->spam = $spam;

        return $this;
    }
}
