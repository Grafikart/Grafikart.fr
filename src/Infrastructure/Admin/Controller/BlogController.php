<?php

namespace App\Infrastructure\Admin\Controller;

use App\Domain\Blog\Event\PostUpdatedEvent;
use App\Domain\Blog\Post;
use App\Domain\Blog\Repository\PostRepository;
use App\Infrastructure\Admin\Data\PostData;
use App\Infrastructure\Admin\Form\PostForm;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class BlogController extends BaseController
{

    /**
     * @Route("/blog", name="blog_index")
     */
    public function index(PostRepository $repo, PaginatorInterface $paginator, Request $request): Response
    {
        $query = $repo
            ->createQueryBuilder('p')
            ->orderBy('p.createdAt', 'DESC');
        if ($request->get('q')) {
            $query = $query->where('p.title LIKE :title')
                ->setParameter('title', "%" . $request->get('q') . "%");
        }
        $page = $request->query->getInt('page', 1);
        $posts = $paginator->paginate(
            $query->getQuery(),
            $page,
            10
        );
        return $this->render('admin/blog/index.html.twig', [
            'posts' => $posts,
            'page' => $page,
            'menu' => 'blog'
        ]);
    }

    /**
     * @Route("/blog/{post}", name="blog_edit")
     */
    public function edit(Post $post, Request $request, EntityManagerInterface $em, EventDispatcherInterface $dispatcher): Response
    {
        $data = PostData::makeFromPost($post);
        $form = $this->createForm(PostForm::class, $data);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data->hydrate($post, $em);
            $em->flush();
            $dispatcher->dispatch(new PostUpdatedEvent($post));
        }

        return $this->render('admin/blog/edit.html.twig', [
            'form' => $form->createView(),
            'post' => $post,
            'menu' => 'blog'
        ]);
    }

}
