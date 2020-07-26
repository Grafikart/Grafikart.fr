<?php

namespace App\Domain\Premium\Entity;

use Doctrine\ORM\Mapping as ORM;

trait PremiumTrait
{
    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?\DateTimeInterface $premiumEnd = null;

    public function isPremium(): bool
    {
        return $this->premiumEnd > new \DateTime();
    }

    public function getPremiumEnd(): ?\DateTimeInterface
    {
        return $this->premiumEnd;
    }

    public function setPremiumEnd(?\DateTimeInterface $premiumEnd): void
    {
        $this->premiumEnd = $premiumEnd;
    }
}
