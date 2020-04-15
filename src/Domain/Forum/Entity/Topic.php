<?php

namespace App\Domain\Forum\Entity;

use App\Domain\Auth\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Domain\Forum\Repository\TopicRepository")
 * @ORM\Table(name="forum_topic")
 */
class Topic
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", length=70)
     * @Assert\NotBlank()
     * @Assert\Length(min="5", max="70")
     */
    private ?string $name = null;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank()
     */
    private ?string $content = null;

    /**
     * @ORM\Column(type="boolean", options={"default":0})
     */
    private ?bool $solved = null;

    /**
     * @ORM\Column(type="boolean", options={"default":0})
     */
    private ?bool $sticky = null;

    /**
     * @ORM\Column(type="datetime")
     */
    private ?\DateTimeInterface $createdAt = null;

    /**
     * @ORM\Column(type="datetime")
     */
    private ?\DateTimeInterface $updatedAt = null;

    /**
     * @ORM\ManyToMany(targetEntity="App\Domain\Forum\Entity\Tag", inversedBy="topics")
     * @ORM\JoinTable(name="forum_topic_tag")
     * @Assert\NotBlank()
     * @Assert\Count(min="1")
     */
    private Collection $tags;

    /**
     * @ORM\ManyToOne(targetEntity="App\Domain\Auth\User", inversedBy="topics")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private ?User $author = null;

    /**
     * @ORM\Column(type="integer", options={"default": 0})
     */
    private int $messageCount = 0;

    /**
     * @ORM\OneToMany(targetEntity="App\Domain\Forum\Entity\Message", mappedBy="topic")
     */
    private Collection $messages;

    /**
     * @ORM\ManyToOne(targetEntity="App\Domain\Forum\Entity\Message")
     * @ORM\JoinColumn(nullable=true)
     */
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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getSolved(): ?bool
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

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
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

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
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
            $this->messages[] = $message;
            $message->setTopic($this);
        }

        return $this;
    }

    public function removeMessage(Message $message): self
    {
        if ($this->messages->contains($message)) {
            $this->messages->removeElement($message);
            // set the owning side to null (unless already changed)
            if ($message->getTopic() === $this) {
                $message->setTopic(null);
            }
        }

        return $this;
    }

    public function getLastMessage(): ?Message
    {
        return $this->lastMessage;
    }

    public function setLastMessage(?Message $lastMessage): self
    {
        $this->lastMessage = $lastMessage;

        return $this;
    }
}
