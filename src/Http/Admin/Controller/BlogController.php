<?php

namespace App\Http\Admin\Controller;

use App\Domain\Blog\Event\PostCreatedEvent;
use App\Domain\Blog\Event\PostDeletedEvent;
use App\Domain\Blog\Event\PostUpdatedEvent;
use App\Domain\Blog\Post;
use App\Http\Admin\Data\PostCrudData;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Permet la gestion du blog
 *
 * @IsGranted("CMS_MANAGE")
 */
final class BlogController extends CrudController
{

    protected string $templatePath = 'blog';
    protected string $menuItem = 'blog';
    protected string $entity = Post::class;
    protected string $routePrefix = 'admin_blog';
    protected array $events = [
        'update' => PostUpdatedEvent::class,
        'delete' => PostDeletedEvent::class,
        'create' => PostCreatedEvent::class
    ];

    /**
     * @Route("/blog", name="blog_index")
     */
    public function index(): Response
    {
        return $this->crudIndex();
    }

    /**
     * @Route("/blog/new", name="blog_new", methods={"POST", "GET"})
     */
    public function new(): Response
    {
        $data = new PostCrudData();
        $data->author = $this->getUser();
        $data->entity = new Post();
        return $this->crudNew($data);
    }

    /**
     * @Route("/blog/{id}", name="blog_edit", methods={"POST", "GET"})
     */
    public function edit(Post $post): Response
    {
        $data = PostCrudData::makeFromPost($post);
        return $this->crudEdit($data);
    }

    /**
     * @Route("/blog/{id}", methods={"DELETE"})
     */
    public function delete(Post $post): Response
    {
        return $this->crudDelete($post);
    }

}
