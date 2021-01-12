<?php

namespace App\Domain\Premium\Entity;

use App\Domain\Auth\User;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Intl\Countries;

/**
 * @ORM\Entity(repositoryClass="App\Domain\Premium\Repository\TransactionRepository")
 */
class Transaction
{
    public const PAYPAL = 'paypal';
    public const STRIPE = 'stripe';

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
     * Prix de l'achat (TVA incluse).
     *
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
    private \DateTimeInterface $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity="App\Domain\Auth\User")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private User $author;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $firstname = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $lastname = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $address = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $city = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $postalCode = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $countryCode = null;

    /**
     * @ORM\Column(type="float", options={"default": 0})
     */
    private float $fee = 0;

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

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): Transaction
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

    public function getDescription(): string
    {
        return "Compte premium {$this->duration} mois";
    }

    public function getFullName(): string
    {
        return $this->lastname.' '.$this->firstname;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(?string $firstname): Transaction
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(?string $lastname): Transaction
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): Transaction
    {
        $this->address = $address;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): Transaction
    {
        $this->city = $city;

        return $this;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function setPostalCode(?string $postalCode): Transaction
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    public function getCountryCode(): ?string
    {
        return $this->countryCode;
    }

    public function setCountryCode(?string $countryCode): Transaction
    {
        $this->countryCode = $countryCode;

        return $this;
    }

    public function getCountry(): ?string
    {
        return Countries::getNames()[$this->countryCode] ?? null;
    }

    public function isPaypal(): bool
    {
        return self::PAYPAL === $this->method;
    }

    public function isStripe(): bool
    {
        return self::STRIPE === $this->method;
    }

    public function getFee(): float
    {
        return $this->fee;
    }

    public function setFee(float $fee): self
    {
        $this->fee = $fee;

        return $this;
    }
}
