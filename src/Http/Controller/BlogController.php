<?php

namespace App\Http\Controller;

use App\Domain\Blog\Category;
use App\Domain\Blog\Post;
use App\Domain\Blog\Repository\CategoryRepository;
use App\Domain\Blog\Repository\PostRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class BlogController extends AbstractController
{

    /**
     * @Route("/blog", name="blog_index")
     */
    public function index(PostRepository $repo, CategoryRepository $categoryRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $page = $request->query->getInt('page', 1);
        $posts = $paginator->paginate(
            $repo->queryAll(),
            $page,
            10
        );
        if ($posts->count() === 0) {
            throw new NotFoundHttpException('Aucun articles ne correspond à cette page');
        }
        return $this->render('blog/index.html.twig', [
            'posts' => $posts,
            'categories' => $categoryRepository->findAll(),
            'page' => $page,
            'menu' => 'blog'
        ]);
    }

    /**
     * @Route("/blog/{slug}", name="blog_show")
     */
    public function show(Post $post): Response
    {
        return $this->render('blog/show.html.twig', [
            'post' => $post,
            'menu' => 'blog'
        ]);
    }

    /**
     * @Route("/blog/category/{slug}", name="blog_category")
     */
    public function category(Request $request, Category $category, PostRepository $repo, CategoryRepository $categoryRepository, PaginatorInterface $paginator): Response
    {
        $page = $request->query->getInt('page', 1);
        $posts = $paginator->paginate(
            $repo->queryAll($category),
            $page,
            10
        );
        if ($posts->count() === 0) {
            throw new NotFoundHttpException('Aucun articles ne correspond à cette page');
        }
        return $this->render('blog/index.html.twig', [
            'posts' => $posts,
            'categories' => $categoryRepository->findAll(),
            'page' => $page,
            'menu' => 'blog'
        ]);
    }

}
