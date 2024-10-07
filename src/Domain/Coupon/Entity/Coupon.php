<?php

namespace App\Domain\Coupon\Entity;

use App\Domain\Auth\User;
use App\Domain\Coupon\Repository\CouponRepository;
use App\Domain\School\Entity\School;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CouponRepository::class)]
class Coupon
{
    #[ORM\Id]
    #[ORM\Column]
    private string $id = '';

    #[ORM\ManyToOne]
    private ?School $school = null;

    #[ORM\Column(nullable: false)]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $claimedAt = null;

    #[ORM\ManyToOne]
    private ?User $claimedBy = null;

    #[ORM\Column]
    private string $email = '';

    #[ORM\Column]
    private int $months = 1;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function setId(string $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getSchool(): ?School
    {
        return $this->school;
    }

    public function setSchool(?School $school): self
    {
        $this->school = $school;

        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getClaimedAt(): ?\DateTimeImmutable
    {
        return $this->claimedAt;
    }

    public function setClaimedAt(?\DateTimeImmutable $claimedAt): self
    {
        $this->claimedAt = $claimedAt;

        return $this;
    }

    public function getClaimedBy(): ?User
    {
        return $this->claimedBy;
    }

    public function setClaimedBy(?User $claimedBy): self
    {
        $this->claimedBy = $claimedBy;

        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getMonths(): int
    {
        return $this->months;
    }

    public function setMonths(int $months): self
    {
        $this->months = $months;
        return $this;
    }

    public function isClaimed(): bool
    {
        return $this->claimedAt !== null;
    }
}
