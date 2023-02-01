<?php

namespace App\Domain\Forum\Entity;

use ApiPlatform\Metadata\ApiProperty;
use App\Domain\Auth\User;
use App\Http\Twig\CacheExtension\CacheableInterface;
use App\Infrastructure\Spam\SpammableInterface;
use App\Infrastructure\Spam\SpamTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: 'forum_topic')]
#[ORM\Entity(repositoryClass: \App\Domain\Forum\Repository\TopicRepository::class)]
class Topic implements SpammableInterface, CacheableInterface
{
    use SpamTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(type: 'integer')]
    #[ApiProperty(identifier: true)]
    #[Groups(['read:topic'])]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 70)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 5, max: 70)]
    #[Groups(['read:topic'])]
    private string $name = '';

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank]
    #[Groups(['read:topic'])]
    private ?string $content = null;

    #[ORM\Column(type: 'boolean', options: ['default' => 0])]
    private ?bool $solved = false;

    #[ORM\Column(type: 'boolean', options: ['default' => 0])]
    private ?bool $sticky = false;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\JoinTable(name: 'forum_topic_tag')]
    #[ORM\ManyToMany(targetEntity: \App\Domain\Forum\Entity\Tag::class, inversedBy: 'topics')]
    #[Assert\NotBlank]
    #[Assert\Count(min: 1, max: 3)]
    #[Groups(['read:topic'])]
    private Collection $tags;

    #[ORM\ManyToOne(targetEntity: \App\Domain\Auth\User::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private User $author;

    #[ORM\Column(type: 'integer', options: ['default' => 0])]
    private int $messageCount = 0;

    #[ORM\OneToMany(targetEntity: \App\Domain\Forum\Entity\Message::class, mappedBy: 'topic')]
    #[ORM\OrderBy(['accepted' => 'DESC', 'createdAt' => 'ASC'])]
    private Collection $messages;

    #[ORM\ManyToOne(targetEntity: \App\Domain\Forum\Entity\Message::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Message $lastMessage = null;

    public function __construct()
    {
        $this->tags = new ArrayCollection();
        $this->messages = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name ?: '';

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content ?: '';

        return $this;
    }

    public function isSolved(): ?bool
    {
        return $this->solved;
    }

    public function setSolved(bool $solved): self
    {
        $this->solved = $solved;

        return $this;
    }

    public function getSticky(): ?bool
    {
        return $this->sticky;
    }

    public function setSticky(bool $sticky): self
    {
        $this->sticky = $sticky;

        return $this;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt ?: new \DateTime();
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return Collection|Tag[]
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(Tag $tag): self
    {
        if (!$this->tags->contains($tag)) {
            $this->tags[] = $tag;
        }

        return $this;
    }

    public function removeTag(Tag $tag): self
    {
        if ($this->tags->contains($tag)) {
            $this->tags->removeElement($tag);
        }

        return $this;
    }

    public function getAuthor(): User
    {
        return $this->author;
    }

    public function setAuthor(User $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getMessageCount(): ?int
    {
        return $this->messageCount;
    }

    public function setMessageCount(int $messageCount): self
    {
        $this->messageCount = $messageCount;

        return $this;
    }

    public function getLastMessage(): ?Message
    {
        return $this->lastMessage;
    }

    /**
     * @return Collection|Message[]
     */
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function addMessage(Message $message): self
    {
        if (!$this->messages->contains($message)) {
            $this->messages->add($message);
            $message->setTopic($this);
        }

        return $this;
    }

    public function setMessages(Collection $messages): self
    {
        $this->messages = $messages;

        return $this;
    }

    public function setLastMessage(?Message $lastMessage): self
    {
        $this->lastMessage = $lastMessage;

        return $this;
    }

    public function isLocked(): bool
    {
        return $this->getCreatedAt() < (new \DateTime('-6 month'));
    }
}
