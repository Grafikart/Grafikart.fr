<?php

namespace App\Http\Controller;

use App\Domain\Blog\Category;
use App\Domain\Blog\Post;
use App\Domain\Blog\Repository\CategoryRepository;
use App\Domain\Blog\Repository\PostRepository;
use Doctrine\ORM\Query;
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
        $title = 'Blog';
        $query = $repo->queryAll();
        return $this->renderListing($title, $query, $paginator, $request);
    }

    /**
     * @Route("/blog/category/{slug}", name="blog_category")
     */
    public function category(Category $category, PostRepository $repo, PaginatorInterface $paginator, Request $request): Response
    {
        $title = $category->getName();
        $query = $repo->queryAll($category);
        return $this->renderListing($title, $query, $paginator, $request);
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

    private function renderListing(string $title, Query $query, PaginatorInterface $paginator, Request $request): Response
    {
        $page = $request->query->getInt('page', 1);
        $posts = $paginator->paginate(
            $query,
            $page,
            10
        );
        if ($page > 1) {
            $title .= ", page $page";
        }
        if ($posts->count() === 0) {
            throw new NotFoundHttpException('Aucun articles ne correspond à cette page');
        }
        return $this->render('blog/index.html.twig', [
            'posts' => $posts,
            'page' => $page,
            'title' => $title,
            'menu' => 'blog'
        ]);
    }

}
