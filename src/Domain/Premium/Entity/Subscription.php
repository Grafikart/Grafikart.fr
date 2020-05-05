<?php

namespace App\Domain\Premium\Entity;

use App\Domain\Auth\User;
use App\Infrastructure\Payment\Stripe\StripeEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Domain\Premium\Repository\PlanRepository")
 */
class Subscription
{

    const ACTIVE = 1;
    const INACTIVE = 0;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer")
     */
    private ?int $id;

    /**
     * @ORM\Column(type="smallint")
     */
    private int $state = self::INACTIVE;

    /**
     * @ORM\Column(type="datetime")
     */
    private \DateTimeInterface $nextPayment;

    /**
     * @ORM\ManyToOne(targetEntity="App\Domain\Premium\Entity\Plan")
     * @ORM\JoinColumn(nullable=false)
     */
    private Plan $plan;

    /**
     * @ORM\ManyToOne(targetEntity="App\Domain\Auth\User")
     * @ORM\JoinColumn(nullable=false)
     */
    private User $user;

    /**
     * @ORM\Column(type="datetime")
     */
    private \DateTimeInterface $createdAt;

    /**
     * @ORM\Column(type="float")
     */
    private float $tax = 0;

    use StripeEntity;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): Subscription
    {
        $this->id = $id;
        return $this;
    }

    public function getState(): int
    {
        return $this->state;
    }

    public function setState(int $state): Subscription
    {
        $this->state = $state;
        return $this;
    }

    public function getNextPayment(): \DateTimeInterface
    {
        return $this->nextPayment;
    }

    public function setNextPayment(\DateTimeInterface $nextPayment): Subscription
    {
        $this->nextPayment = $nextPayment;
        return $this;
    }

    public function getPlan(): Plan
    {
        return $this->plan;
    }

    public function setPlan(Plan $plan): Subscription
    {
        $this->plan = $plan;
        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): Subscription
    {
        $this->user = $user;
        return $this;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): Subscription
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getTax(): float
    {
        return $this->tax;
    }

    public function setTax(float $tax): self
    {
        $this->tax = $tax;
        return $this;
    }


}
