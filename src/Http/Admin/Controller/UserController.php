<?php

namespace App\Http\Admin\Controller;

use App\Domain\Auth\User;
use App\Domain\Auth\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends CrudController
{
    protected string $templatePath = 'user';
    protected string $menuItem = 'user';
    protected string $entity = User::class;
    protected string $routePrefix = 'admin_user';
    protected string $searchField = 'username';
    protected array $events = [];

    /**
     * @Route("/users", name="user_index")
     */
    public function index(): Response
    {
        return $this->crudIndex();
    }

    public function applySearch(string $search, QueryBuilder $query): QueryBuilder
    {
        return $query->where('LOWER(row.username) = :search')
            ->orWhere('row.email = :search')
            ->setParameter('search', strtolower($search));
    }

    /**
     * @Route("/users/search/{q?}", name="user_autocomplete")
     */
    public function search(string $q): JsonResponse
    {
        /** @var UserRepository $repository */
        $repository = $this->em->getRepository(User::class);
        $q = strtolower($q);
        if ('moi' === $q) {
            return new JsonResponse([[
                'id' => $this->getUser()->getId(),
                'username' => $this->getUser()->getUsername(),
            ]]);
        }
        $users = $repository
            ->createQueryBuilder('u')
            ->select('u.id', 'u.username')
            ->where('LOWER(u.username) LIKE :username')
            ->setParameter('username', "%$q%")
            ->setMaxResults(25)
            ->getQuery()
            ->getResult();

        return new JsonResponse($users);
    }

    /**
     * @Route("/users/{id}/ban", methods={"POST"}, name="user_ban")
     */
    public function ban(User $user, EntityManagerInterface $em): RedirectResponse
    {
        $username = $user->getUsername();
        $em->remove($user);
        $em->flush();
        $this->addFlash('success', "L'utilisateur $username a été banni");

        return $this->redirectToRoute('admin_home');
    }
}
