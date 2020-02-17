<?php

namespace App\Infrastructure\Admin\Controller;

use App\Domain\Blog\Event\PostUpdatedEvent;
use App\Domain\Blog\Post;
use App\Infrastructure\Admin\Data\PostCrudData;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class BlogController extends CrudController
{

    protected string $templatePath = 'blog';
    protected string $menuItem = 'blog';
    protected string $entity = Post::class;
    protected array $events = [
        'update' => PostUpdatedEvent::class,
        'delete' => '',
        'create' => ''
    ];

    /**
     * @Route("/blog", name="blog_index")
     */
    public function index(): Response
    {
        return $this->crudIndex();
    }

    /**
     * @Route("/blog/{post}", name="blog_edit")
     */
    public function edit(Post $post): Response
    {
        $data = PostCrudData::makeFromPost($post);
        return $this->crudEdit($data);
    }

}
