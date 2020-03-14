<?php

namespace App\Http\Admin\Data;

use App\Domain\Attachment\Attachment;
use App\Domain\Auth\User;
use App\Domain\Blog\Category;
use App\Domain\Blog\Post;
use App\Http\Admin\Form\PostForm;
use Symfony\Component\Validator\Constraints as Assert;

final class PostCrudData implements CrudDataInterface
{

    /**
     * @Assert\NotBlank()
     */
    public ?string $title = null;

    public ?string $slug = null;

    public ?Attachment $image = null;

    public ?Category $category = null;

    public ?\DateTimeInterface $createdAt;

    /**
     * @Assert\NotBlank()
     */
    public ?User $author;

    /**
     * @Assert\NotBlank()
     */
    public ?string $content = null;

    public bool $online = false;

    public Post $entity;

    public static function makeFromPost(Post $post): self
    {
        $data = new self();
        $data->title = $post->getTitle();
        $data->slug = $post->getSlug();
        $data->category = $post->getCategory();
        $data->createdAt = $post->getCreatedAt();
        $data->content = $post->getContent();
        $data->author = $post->getAuthor();
        $data->online = $post->isOnline();
        $data->image = $post->getImage();
        $data->entity = $post;
        return $data;
    }

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function hydrate(): void
    {
        $this->entity
            ->setCategory($this->category)
            ->setImage($this->image)
            ->setTitle($this->title)
            ->setCreatedAt($this->createdAt)
            ->setContent($this->content)
            ->setOnline($this->online)
            ->setUpdatedAt(new \DateTime())
            ->setAuthor($this->author)
            ->setSlug($this->slug);
    }

    public function getEntity(): object
    {
        return $this->entity;
    }

    public function getFormClass(): string
    {
        return PostForm::class;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(User $author): PostCrudData
    {
        $this->author = $author;
        return $this;
    }

}
