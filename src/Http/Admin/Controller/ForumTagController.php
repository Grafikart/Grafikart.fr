<?php

namespace App\Http\Admin\Controller;

use App\Domain\Blog\Post;
use App\Domain\Forum\Entity\Tag;
use App\Http\Admin\Data\ForumTagCrudData;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Permet la gestion des tags sur le forum
 */
final class ForumTagController extends CrudController
{

    protected string $templatePath = 'forum/tag';
    protected string $menuItem = 'forum-tag';
    protected string $entity = Tag::class;
    protected string $routePrefix = 'admin_forum-tag';

    /**
     * @Route("/forum-tag", name="forum-tag_index")
     */
    public function index(): Response
    {
        return $this->crudIndex();
    }

    /**
     * @Route("/forum-tag/new", name="forum-tag_new")
     */
    public function new(): Response
    {
        $tag = (new Tag())->setCreatedAt(new \DateTime());
        $data = new ForumTagCrudData($tag);
        return $this->crudNew($data);
    }

    /**
     * @Route("/forum-tag/{id}", name="forum-tag_edit", methods={"POST", "GET"})
     */
    public function edit(Tag $tag): Response
    {
        $data = new ForumTagCrudData($tag);
        return $this->crudEdit($data);
    }

    /**
     * @Route("/forum-tag/{id}", methods={"DELETE"})
     */
    public function delete(Post $post): Response
    {
        return $this->crudDelete($post);
    }

}
