<?php

namespace App\Http\Admin\Data;

use App\Domain\Auth\User;
use App\Domain\Blog\Category;
use App\Domain\Blog\Post;
use App\Http\Admin\Form\PostForm;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;


final class PostCrudData implements CrudDataInterface
{

    /**
     * @Assert\NotBlank()
     */
    public string $title;

    public string $slug;

    public ?UploadedFile $image = null;

    public ?Category $category;

    public \DateTimeInterface $createdAt;

    /**
     * @Assert\NotBlank()
     */
    public int $author = 1;

    /**
     * @Assert\NotBlank()
     */
    public string $content;

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
        $data->author = $post->getAuthor()->getId();
        $data->online = $post->isOnline();
        $data->entity = $post;
        return $data;
    }

    /**
     * @param Post $post
     */
    public function hydrate(Post $post, EntityManagerInterface $em): Post
    {
        if ($post->getImage() !== null && $this->image) {
            $post->getImage()
                ->setCreatedAt(new \DateTime())
                ->setFile($this->image);
        }
        /** @var User $user */
        $user = $em->getReference(User::class, $this->author);
        /** @var Post $post */
        $post = $post
            ->setCategory($this->category)
            ->setTitle($this->title)
            ->setCreatedAt($this->createdAt)
            ->setContent($this->content)
            ->setOnline($this->online)
            ->setUpdatedAt(new \DateTime())
            ->setAuthor($user)
            ->setSlug($this->slug);
        return $post;
    }

    public function getEntity(): object
    {
        return $this->entity;
    }

    public function getFormClass(): string
    {
        return PostForm::class;
    }
}
