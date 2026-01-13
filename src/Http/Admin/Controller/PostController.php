<?php

namespace App\Http\Admin\Controller;

use App\Domain\Attachment\ObjectMapper\AttachmentUrlTransformer;
use App\Domain\Blog\Category;
use App\Domain\Blog\Post;
use App\Http\Admin\Data\Post\PostFormData;
use App\Http\Admin\Data\Post\PostFormInput;
use App\Http\Admin\Data\Post\PostRowData;
use App\Http\Data\AttachmentUrlData;
use App\Http\Data\OptionItemData;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/posts', name: 'post_')]
final class PostController extends InertiaController
{
    protected string $entityClass = Post::class;
    protected string $routePrefix = 'post';
    protected string $componentDirectory = 'posts';
    protected string $itemDataClass = PostRowData::class;
    protected string $formDataClass = PostFormData::class;
    protected string $inputDataClass = PostFormInput::class;

    #[Route(path: '/', name: 'index')]
    public function index(): Response
    {
        $query = $this->em->createQueryBuilder()
            ->select(sprintf(
                'NEW %s(p.id, p.title, p.online)',
                PostRowData::class
            ))
            ->from(Post::class, 'p')
            ->orderBy('p.createdAt', 'DESC');

        $pagination = $this->paginator->paginate($query->getQuery());

        return $this->renderComponent('posts/index', [
            'pagination' => $pagination,
        ]);
    }

    #[Route(path: '/{id<\d+>}', name: 'edit', methods: ['GET'])]
    public function edit(
        Post $post,
        AttachmentUrlTransformer $attachmentUrlTransformer,
    ): Response {
        return $this->renderComponent('posts/form', [
            'item' => $this->buildFormData($post, $attachmentUrlTransformer),
        ]);
    }

    #[Route(path: '/new', name: 'create', methods: ['GET'])]
    public function create(): Response
    {
        return $this->renderComponent('posts/form', [
            'item' => new PostFormData(
                categories: $this->getCategories(),
            ),
        ]);
    }

    #[Route(path: '/{id<\d+>}', name: 'update', methods: ['POST'])]
    public function update(
        Post $post,
        #[MapRequestPayload]
        PostFormInput $data,
    ): Response {
        return $this->crudUpdate(data: $data, entity: $post);
    }

    #[Route(path: '/new', name: 'store', methods: ['POST'])]
    public function store(
        #[MapRequestPayload]
        PostFormInput $data,
    ): Response {
        $post = (new Post())
            ->setUpdatedAt(new \DateTimeImmutable());

        return $this->crudStore(data: $data, entity: $post);
    }

    #[Route(path: '/{id<\d+>}', name: 'delete', methods: ['DELETE'])]
    public function delete(Post $post): Response
    {
        $post->setOnline(false);
        $post->setUpdatedAt(new \DateTimeImmutable());
        $this->em->flush();

        return $this->redirectToInertiaRoute('admin_post_index');
    }

    private function buildFormData(
        Post $post,
        AttachmentUrlTransformer $attachmentUrlTransformer,
    ): PostFormData {
        $image = null;
        if ($post->getImage()) {
            $image = new AttachmentUrlData(
                id: $post->getImage()->getId(),
                url: $attachmentUrlTransformer($post->getImage(), $post->getImage(), null),
            );
        }

        return new PostFormData(
            id: $post->getId(),
            title: $post->getTitle() ?? '',
            slug: $post->getSlug() ?? '',
            createdAt: $post->getCreatedAt(),
            category: $post->getCategory()?->getId(),
            online: $post->isOnline(),
            image: $image,
            content: $post->getContent() ?? '',
            categories: $this->getCategories(),
        );
    }

    /**
     * @return OptionItemData[]
     */
    private function getCategories(): array
    {
        return $this->em->createQueryBuilder()
            ->select(sprintf('NEW %s(c.id, c.name)', OptionItemData::class))
            ->from(Category::class, 'c')
            ->orderBy('c.name', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
