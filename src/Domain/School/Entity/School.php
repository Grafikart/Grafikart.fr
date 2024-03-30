<?php

namespace App\Domain\School\Entity;

use App\Domain\Auth\User;
use App\Domain\School\Repository\SchoolRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SchoolRepository::class)]
class School
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private string $name = '';

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private string $emailMessage = '';

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private string $emailSubject = '';

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private string $couponPrefix = '';

    #[ORM\Column(options: ['default' => 0])]
    private int $credits = 0;

    #[ORM\ManyToMany(targetEntity: User::class)]
    private Collection $students;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $owner = null;

    public function __construct()
    {
        $this->students = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getCredits(): int
    {
        return $this->credits;
    }

    public function setCredits(int $credits): self
    {
        $this->credits = $credits;

        return $this;
    }

    public function getEmailSubject(): string
    {
        return $this->emailSubject;
    }

    public function setEmailSubject(string $emailSubject): self
    {
        $this->emailSubject = $emailSubject;
        return $this;
    }

    public function getCouponPrefix(): string
    {
        return $this->couponPrefix;
    }

    public function setCouponPrefix(string $couponPrefix): self
    {
        $this->couponPrefix = $couponPrefix;
        return $this;
    }

    public function getEmailMessage(): string
    {
        return $this->emailMessage;
    }

    public function setEmailMessage(string $emailMessage): School
    {
        $this->emailMessage = $emailMessage;
        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getStudents(): Collection
    {
        return $this->students;
    }

    public function addStudent(User $student): self
    {
        if (!$this->students->contains($student)) {
            $this->students->add($student);
        }

        return $this;
    }

    public function removeStudent(User $student): self
    {
        $this->students->removeElement($student);

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }
}
