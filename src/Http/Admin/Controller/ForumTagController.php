<?php

namespace App\Http\Admin\Controller;

use App\Domain\Forum\Entity\Tag;
use App\Domain\Forum\Repository\TagRepository;
use App\Http\Admin\Data\ForumTagCrudData;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Permet la gestion des tags sur le forum.
 */
final class ForumTagController extends CrudController
{
    protected string $templatePath = 'forum/tag';
    protected string $menuItem = 'forum-tag';
    protected string $entity = Tag::class;
    protected string $searchField = 'name';
    protected string $routePrefix = 'admin_forum-tag';

    /**
     * @Route("/forum-tag", name="forum-tag_index")
     */
    public function index(SerializerInterface $serializer, TagRepository $tagRepository, Request $request): Response
    {
        return $this->render("admin/{$this->templatePath}/index.html.twig", [
            'tags' => $serializer->serialize($tagRepository->findTree(), 'json'),
            'menu' => $this->menuItem,
            'prefix' => $this->routePrefix,
        ]);
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
     * @Route("/forum-tag/{id<\d+>}", name="forum-tag_edit", methods={"POST", "GET"})
     */
    public function edit(Tag $tag): Response
    {
        $data = new ForumTagCrudData($tag);

        return $this->crudEdit($data);
    }

    /**
     * @Route("/forum-tag/{id}", methods={"DELETE"})
     */
    public function delete(Request $request, Tag $post): Response
    {
        $response = $this->crudDelete($post);
        if (in_array('application/json', $request->getAcceptableContentTypes())) {
            return new JsonResponse([]);
        }

        return $response;
    }

    /**
     * Mémorise la position des tags dans la base de données.
     *
     * ## Requête
     *
     * {id:<int>, position:<int>, parent<int>}[]
     *
     * @Route("/forum-tag/positions", methods={"POST"}, name="forum-tag_positions")
     */
    public function sort(Request $request, TagRepository $tagRepository, EntityManagerInterface $em): Response
    {
        ['positions' => $positions] = json_decode((string) $request->getContent(), true);
        $positionById = array_reduce($positions, function ($acc, $position) {
            $acc[$position['id']] = $position;

            return $acc;
        }, []);
        $tags = $tagRepository->findBy(['id' => array_keys($positionById)]);
        foreach ($tags as $tag) {
            $position = $positionById[$tag->getId()];
            $parent = null;
            if ($position['parent'] > 0) {
                /** @var Tag $parent */
                $parent = $this->em->getReference(Tag::class, (int) $position['parent']);
            }
            $tag
                ->setParent($parent)
                ->setPosition($position['position'] + 1);
        }
        $em->flush();

        return new JsonResponse([], 200);
    }
}
