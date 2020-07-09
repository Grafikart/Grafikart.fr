<?php

namespace App\Domain\Premium\Entity;

use App\Infrastructure\Payment\Stripe\StripeEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Domain\Premium\Repository\PlanRepository")
 */
class Plan
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer")
     */
    private ?int $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private string $name = '';

    /**
     * @ORM\Column(type="float", nullable=false)
     */
    private float $price = 0;

    /**
     * Durée de l'abonnement (en mois).
     *
     * @ORM\Column(type="integer", nullable=false)
     */
    private int $duration = 1;

    use StripeEntity;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): Plan
    {
        $this->id = $id;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): Plan
    {
        $this->name = $name;

        return $this;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function setPrice(float $price): Plan
    {
        $this->price = $price;

        return $this;
    }

    public function getDuration(): int
    {
        return $this->duration;
    }

    public function setDuration(int $duration): Plan
    {
        $this->duration = $duration;

        return $this;
    }

    public function getStripeId(): ?string
    {
        return $this->stripeId;
    }

    public function setStripeId(?string $stripeId): Plan
    {
        $this->stripeId = $stripeId;

        return $this;
    }
}
