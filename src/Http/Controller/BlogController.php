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
    private PostRepository $postRepository;
    private CategoryRepository $categoryRepository;
    private PaginatorInterface $paginator;

    public function __construct(PostRepository $postRepository, CategoryRepository $categoryRepository, PaginatorInterface $paginator)
    {
        $this->postRepository = $postRepository;
        $this->categoryRepository = $categoryRepository;
        $this->paginator = $paginator;
    }

    /**
     * @Route("/blog", name="blog_index")
     */
    public function index(Request $request): Response
    {
        $title = 'Blog';
        $query = $this->postRepository->queryAll();

        return $this->renderListing($title, $query, $request);
    }

    /**
     * @Route("/blog/category/{slug}", name="blog_category")
     */
    public function category(Category $category, Request $request): Response
    {
        $title = $category->getName();
        $query = $this->postRepository->queryAll($category);

        return $this->renderListing($title, $query, $request, ['category' => $category]);
    }

    /**
     * @Route("/blog/{slug}", name="blog_show")
     */
    public function show(Post $post): Response
    {
        return $this->render('blog/show.html.twig', [
            'post' => $post,
            'menu' => 'blog',
        ]);
    }

    private function renderListing(string $title, Query $query, Request $request, array $params = []): Response
    {
        $page = $request->query->getInt('page', 1);
        $posts = $this->paginator->paginate(
            $query,
            $page,
            10
        );
        if ($page > 1) {
            $title .= ", page $page";
        }
        if (0 === $posts->count()) {
            throw new NotFoundHttpException('Aucun articles ne correspond à cette page');
        }
        $categories = $this->categoryRepository->findWithCount();

        return $this->render('blog/index.html.twig', array_merge([
            'posts' => $posts,
            'categories' => $categories,
            'page' => $page,
            'title' => $title,
            'menu' => 'blog',
        ], $params));
    }
}
