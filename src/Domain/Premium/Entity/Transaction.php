<?php

namespace App\Domain\Premium\Entity;

use App\Domain\Auth\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Domain\Premium\Repository\TransactionRepository")
 */
class Transaction
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="integer")
     */
    private int $duration;

    /**
     * @ORM\Column(type="float")
     */
    private float $price;

    /**
     * @ORM\Column(type="float")
     */
    private float $tax;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $method;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $methodRef;

    /**
     * @ORM\Column(type="boolean", options={"default": false})
     */
    private bool $refunded = false;

    /**
     * @ORM\Column(type="datetime")
     */
    private \DateTime $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity="App\Domain\Auth\User")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private User $author;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): Transaction
    {
        $this->id = $id;

        return $this;
    }

    public function getDuration(): int
    {
        return $this->duration;
    }

    public function setDuration(int $duration): Transaction
    {
        $this->duration = $duration;

        return $this;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function setPrice(float $price): Transaction
    {
        $this->price = $price;

        return $this;
    }

    public function getTax(): float
    {
        return $this->tax;
    }

    public function setTax(float $tax): Transaction
    {
        $this->tax = $tax;

        return $this;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function setMethod(string $method): Transaction
    {
        $this->method = $method;

        return $this;
    }

    public function getMethodRef(): ?string
    {
        return $this->methodRef;
    }

    public function setMethodRef(?string $methodRef): Transaction
    {
        $this->methodRef = $methodRef;

        return $this;
    }

    public function isRefunded(): bool
    {
        return $this->refunded;
    }

    public function setRefunded(bool $refunded): Transaction
    {
        $this->refunded = $refunded;

        return $this;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): Transaction
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getAuthor(): User
    {
        return $this->author;
    }

    public function setAuthor(User $author): Transaction
    {
        $this->author = $author;

        return $this;
    }
}
