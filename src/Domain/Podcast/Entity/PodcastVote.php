<?php

namespace App\Domain\Podcast\Entity;

use App\Domain\Auth\User;
use App\Domain\Podcast\Repository\PodcastVoteRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PodcastVoteRepository::class)]
class PodcastVote
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'SEQUENCE')]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'float', options: ['default' => 1])]
    private float $weight = 1;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeInterface $createdAt;

    public function __construct(
        #[ORM\ManyToOne(targetEntity: User::class)]
        #[ORM\JoinColumn(nullable: false)]
        private User $voter,
        #[ORM\ManyToOne(targetEntity: Podcast::class)]
        #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
        private Podcast $podcast
    ) {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPodcast(): Podcast
    {
        return $this->podcast;
    }

    public function setPodcast(Podcast $podcast): self
    {
        $this->podcast = $podcast;

        return $this;
    }

    public function getVoter(): User
    {
        return $this->voter;
    }

    public function setVoter(User $voter): self
    {
        $this->voter = $voter;

        return $this;
    }

    public function getWeight(): float
    {
        return $this->weight;
    }

    public function setWeight(float $weight): self
    {
        $this->weight = $weight;

        return $this;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
