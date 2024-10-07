<?php

namespace App\Domain\Premium\Entity;

use Doctrine\ORM\Mapping as ORM;

trait PremiumTrait
{
    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    protected ?\DateTimeImmutable $premiumEnd = null;

    public function isPremium(): bool
    {
        return $this->premiumEnd > new \DateTimeImmutable();
    }

    public function getPremiumEnd(): ?\DateTimeImmutable
    {
        return $this->premiumEnd;
    }

    public function setPremiumEnd(?\DateTimeImmutable $premiumEnd): self
    {
        $this->premiumEnd = $premiumEnd;

        return $this;
    }

    public function addPremiumMonths(int $months): self
    {
        $now = new \DateTimeImmutable();
        $premiumEnd = $this->getPremiumEnd() ?: new \DateTimeImmutable();
        // Si l'utilisateur a déjà une date de fin de premium dans le futur, alors on incrémentera son compte
        $premiumEnd = $premiumEnd > $now ? $premiumEnd : new \DateTimeImmutable();
        $this->setPremiumEnd($premiumEnd->add(new \DateInterval("P{$months}M")));

        return $this;
    }
}
