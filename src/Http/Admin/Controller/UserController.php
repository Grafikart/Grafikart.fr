<?php

namespace App\Http\Admin\Controller;

use App\Domain\Auth\Service\UserBanService;
use App\Domain\Auth\User;
use App\Domain\Auth\UserRepository;
use App\Domain\Premium\Exception\PremiumNotBanException;
use App\Domain\Stats\UserStatsRepository;
use App\Http\Admin\Data\User\UserItemData;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * @method UserRepository getRepository()
 */
#[Route(path: '/users', name: 'user_')]
final class UserController extends InertiaController
{
    protected string $entityClass = User::class;
    protected string $routePrefix = 'user';
    protected string $componentDirectory = 'users';
    protected string $itemDataClass = UserItemData::class;

    #[Route(path: '/', name: 'index')]
    public function index(Request $request, UserStatsRepository $repository): Response
    {
        $query = $this->getRepository()
            ->createQueryBuilder('row')
            ->orderBy('row.createdAt', 'DESC');
        $filterBanned = $request->get('banned');
        $params = [
            'banned_filter' => $filterBanned
        ];
        if (
            $request->query->getInt('page', 1) === 1
            && !$filterBanned
            && !$request->query->get('q')
        ) {
            $params['months'] = $repository->getMonthlySignups();
            $params['days'] = $repository->getDailySignups();
        }
        if ($filterBanned) {
            $query = $this->getRepository()->queryBanned();
        }

        return $this->crudIndex($query, $params);
    }

    #[Route(path: '/{id<\d+>}/ban', methods: ['DELETE'], name: 'ban')]
    public function ban(User $user, EntityManagerInterface $em, UserBanService $banService, Request $request): Response
    {
        $username = $user->getUsername();
        try {
            $banService->ban($user);
            $em->flush();
        } catch (PremiumNotBanException) {
            $this->addFlash('error', 'Impossible de bannir un utilisateur premium');

            return $this->redirectBack('admin_user_index');
        }

        $this->addFlash('success', "L'utilisateur $username a été banni");

        return $this->redirectBack('admin_user_index');
    }

}
