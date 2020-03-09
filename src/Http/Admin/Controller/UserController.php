<?php

namespace App\Http\Admin\Controller;

use App\Domain\Auth\User;
use App\Domain\Auth\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends BaseController
{

    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/users/search/{q?}", name="user_autocomplete")
     */
    public function search(string $q): JsonResponse
    {
        /** @var UserRepository $repository */
        $repository = $this->em->getRepository(User::class);
        $q = strtolower($q);
        if ($q === 'moi') {
            return new JsonResponse([[
                'id' => $this->getUser()->getId(),
                'username' => $this->getUser()->getUsername()
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

}
