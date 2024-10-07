<?php

namespace App\Domain\Blog;

use App\Domain\Application\Entity\Content;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table('blog_post')]
#[ORM\Entity(repositoryClass: Repository\PostRepository::class)]
class Post extends Content
{
    #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'posts')]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    private ?Category $category = null;

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function hasVideo(): bool
    {
        if (null !== $this->getContent()) {
            return 1 === preg_match('/^[^\s]*youtube.com/mi', $this->getContent());
        }

        return false;
    }
}
